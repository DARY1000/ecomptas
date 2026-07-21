<?php

namespace App\Services;

use App\Models\ModeleExport;
use App\Models\Tenant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

/**
 * Permet à un cabinet (plan Pro+) d'uploader son propre modèle de fichier
 * d'écritures comptables. Une IA (GPT-4o) analyse une seule fois sa structure
 * (en-têtes, correspondance avec nos champs d'écriture, conventions de style)
 * pour ensuite pouvoir régénérer un export dans exactement ce format.
 */
class ModeleExportService
{
    private string $openaiKey;
    private string $gptModel;

    public function __construct(private ExportService $exportService)
    {
        $this->openaiKey = config('services.openai.api_key', '');
        $this->gptModel  = config('services.openai.model', 'gpt-4o');
    }

    /**
     * Stocke le fichier modèle uploadé (un seul par cabinet — remplace le précédent).
     * L'analyse IA est déclenchée séparément, en file d'attente (AnalyserModeleExport).
     */
    public function importer(Tenant $tenant, UploadedFile $file): ModeleExport
    {
        $ancien = ModeleExport::where('tenant_id', $tenant->id)->first();
        if ($ancien && Storage::disk('public')->exists($ancien->chemin_fichier)) {
            Storage::disk('public')->delete($ancien->chemin_fichier);
        }

        $chemin = $file->store("tenants/{$tenant->id}/modeles", 'public');

        return ModeleExport::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'nom_original'   => $file->getClientOriginalName(),
                'chemin_fichier' => $chemin,
                'extension'      => strtolower($file->getClientOriginalExtension()),
                'structure'      => null,
                'notes_style'    => null,
                'analyse_le'     => null,
            ]
        );
    }

    public function supprimer(ModeleExport $modele): void
    {
        if (Storage::disk('public')->exists($modele->chemin_fichier)) {
            Storage::disk('public')->delete($modele->chemin_fichier);
        }
        $modele->delete();
    }

    /**
     * Lit les en-têtes et un échantillon de lignes du fichier, puis demande à
     * GPT-4o de faire correspondre les colonnes avec nos champs d'écriture
     * standards et de résumer les conventions de style observées.
     */
    public function analyser(ModeleExport $modele): void
    {
        $path = Storage::disk('public')->path($modele->chemin_fichier);

        $spreadsheet  = IOFactory::load($path);
        $lignesBrutes = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $lignesBrutes = array_values(array_filter($lignesBrutes, fn ($l) => count(array_filter($l, fn ($v) => $v !== null && $v !== '')) > 0));

        if (empty($lignesBrutes)) {
            throw new \RuntimeException('Fichier vide ou illisible.');
        }

        $entetes     = array_map(fn ($v) => trim((string) $v), $lignesBrutes[0]);
        $echantillon = array_slice($lignesBrutes, 1, 15);

        if (empty($this->openaiKey)) {
            throw new \RuntimeException('OPENAI_API_KEY non configurée');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiKey,
            'Content-Type'  => 'application/json',
        ])
        ->timeout(60)
        ->post('https://api.openai.com/v1/chat/completions', [
            'model'       => $this->gptModel,
            'temperature' => 0,
            'tool_choice' => ['type' => 'function', 'function' => ['name' => 'analyser_modele']],
            'tools'       => [$this->schemaAnalyse()],
            'messages'    => [
                ['role' => 'system', 'content' => "Tu es un expert-comptable SYSCOHADA. On te fournit un extrait d'un fichier d'écritures comptables tel qu'un cabinet le produit habituellement. Ton rôle : identifier précisément ses colonnes, faire correspondre chacune avec nos champs standards, et résumer ses conventions propres (numérotation des comptes, formulation des libellés, codes de journaux) pour qu'un système puisse reproduire fidèlement ce format plus tard."],
                ['role' => 'user',   'content' => $this->formaterEchantillon($entetes, $echantillon)],
            ],
        ]);

        if (!$response->successful()) {
            Log::error('ModeleExportService: analyse échouée', [
                'tenant_id' => $modele->tenant_id,
                'status'    => $response->status(),
                'body'      => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException("L'analyse du modèle a échoué (HTTP {$response->status()}).");
        }

        $toolCall  = $response->json('choices.0.message.tool_calls.0');
        $arguments = $toolCall['function']['arguments'] ?? '{}';
        $resultat  = json_decode($arguments, true);

        if (!$resultat) {
            throw new \RuntimeException("L'analyse du modèle a renvoyé une réponse invalide.");
        }

        $modele->update([
            'structure' => [
                'colonnes'       => $resultat['colonnes'] ?? $entetes,
                'correspondance' => $resultat['correspondance'] ?? [],
            ],
            'notes_style' => $resultat['notes_style'] ?? null,
            'analyse_le'  => now(),
        ]);
    }

    /**
     * Régénère un fichier dans la structure exacte du modèle uploadé, rempli avec
     * les écritures réelles du cabinet pour la période demandée.
     */
    public function genererExport(
        Tenant       $tenant,
        ModeleExport $modele,
        ?string      $dateDebut = null,
        ?string      $dateFin   = null,
        ?string      $journal   = null
    ): string {
        $colonnes = $modele->structure['colonnes'] ?? null;
        abort_if(empty($colonnes), 422, "Ce modèle n'a pas encore été analysé.");

        $correspondance = $modele->structure['correspondance'] ?? [];

        $ecritures = $this->exportService
            ->requeteEcritures($tenant->id, $dateDebut, $dateFin, $journal)
            ->load('facture');

        $lignes = [$colonnes];

        foreach ($ecritures as $e) {
            $valeurs = [
                'journal'    => $e->journal,
                'date'       => $e->date_ecriture?->format('d/m/Y') ?? '',
                'compte_num' => $e->numero_compte,
                'compte_lib' => $e->libelle_compte ?? '',
                'piece_ref'  => $e->numero_piece ?? '',
                'tiers'      => $e->facture?->fournisseur_client ?? '',
                'libelle'    => $e->libelle_ecriture,
                'debit'      => $e->debit > 0 ? number_format((float) $e->debit, 2, ',', ' ') : '',
                'credit'     => $e->credit > 0 ? number_format((float) $e->credit, 2, ',', ' ') : '',
            ];

            $ligne = [];
            foreach ($colonnes as $entete) {
                $champ    = array_search($entete, $correspondance, true);
                $ligne[]  = $champ !== false ? ($valeurs[$champ] ?? '') : '';
            }
            $lignes[] = $ligne;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($lignes, null, 'A1');

        $writer = $modele->extension === 'csv'
            ? new CsvWriter($spreadsheet)
            : new XlsxWriter($spreadsheet);

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    private function formaterEchantillon(array $entetes, array $echantillon): string
    {
        $texte = "En-têtes (dans l'ordre) :\n" . implode(' | ', $entetes) . "\n\nÉchantillon de lignes :\n";
        foreach ($echantillon as $ligne) {
            $texte .= implode(' | ', array_map(fn ($v) => (string) $v, $ligne)) . "\n";
        }

        return $texte;
    }

    private function schemaAnalyse(): array
    {
        $champProps = ['type' => ['string', 'null']];

        return [
            'type'     => 'function',
            'function' => [
                'name'        => 'analyser_modele',
                'description' => "Identifie les colonnes d'un fichier d'écritures comptables et leur correspondance avec nos champs standards.",
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'colonnes' => [
                            'type'        => 'array',
                            'description' => "Liste ordonnée des en-têtes de colonnes exactement comme dans le fichier.",
                            'items'       => ['type' => 'string'],
                        ],
                        'correspondance' => [
                            'type'        => 'object',
                            'description' => "Pour chaque champ standard, le nom exact (issu de 'colonnes') de la colonne qui lui correspond, ou null si absente du fichier.",
                            'properties'  => [
                                'journal'    => $champProps,
                                'date'       => $champProps,
                                'compte_num' => $champProps,
                                'compte_lib' => $champProps,
                                'piece_ref'  => $champProps,
                                'tiers'      => $champProps,
                                'libelle'    => $champProps,
                                'debit'      => $champProps,
                                'credit'     => $champProps,
                            ],
                            'required' => ['journal', 'date', 'compte_num', 'compte_lib', 'piece_ref', 'tiers', 'libelle', 'debit', 'credit'],
                        ],
                        'notes_style' => [
                            'type'        => 'string',
                            'description' => "Résumé court (3 à 5 phrases) des conventions propres au cabinet observées dans l'échantillon : précision des numéros de compte (ex. sous-comptes à 5 chiffres), formulation des libellés, codes de journaux utilisés. Chaîne vide si aucun échantillon exploitable.",
                        ],
                    ],
                    'required' => ['colonnes', 'correspondance', 'notes_style'],
                ],
            ],
        ];
    }
}
