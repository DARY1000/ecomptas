<?php

namespace App\Services;

use App\Models\Facture;
use App\Models\ModeleExport;
use App\Models\TraitementLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Service de traitement IA direct — sans n8n.
 *
 * Pipeline :
 *  1. Lire le fichier PDF/image depuis le stockage local (Hostinger)
 *  2. OCR :
 *     - PDF       → Mistral OCR (mistral-ocr-latest)
 *     - JPG/PNG   → Mistral Vision (pixtral-large-latest)
 *  3. Classification + Extraction → GPT-4o function calling (OpenAI)
 *  4. Génération des écritures SYSCOHADA → SYSCOHADAService
 *
 * Avantages vs n8n.cloud :
 *  - Pas d'abonnement n8n (~20$/mois)
 *  - Latence réduite (1 job, 2 appels API)
 *  - Code 100% sous contrôle (débogage facilité)
 *  - Fonctionne sur Hostinger mutualisé (queue database, pas de Redis)
 */
class TraitementIAService
{
    private string $mistralKey;
    private string $openaiKey;
    private string $ocrModel;
    private string $visionModel;
    private string $gptModel;

    public function __construct(private SYSCOHADAService $syscohada)
    {
        $this->mistralKey  = config('services.mistral.api_key', '');
        $this->openaiKey   = config('services.openai.api_key', '');
        $this->ocrModel    = config('services.mistral.model', 'mistral-ocr-latest');
        $this->visionModel = config('services.mistral.vision_model', 'pixtral-large-latest');
        $this->gptModel    = config('services.openai.model', 'gpt-4o');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Point d'entrée principal
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Traite une facture de bout en bout.
     * Appelé depuis TraiterFacturePDF::handle().
     *
     * @throws \RuntimeException si l'une des étapes échoue
     */
    public function traiter(Facture $facture): void
    {
        $debut = microtime(true);

        // 1. Charger le fichier
        $contenu = $this->lireFichier($facture);
        $mime    = $this->detecterMime($facture->pdf_nom_original ?? '');

        // 2. OCR
        $texteOcr = $this->ocr($contenu, $mime, $facture->id);
        $facture->update([
            'statut'    => 'traitement_en_cours',
            'ocr_texte' => mb_substr($texteOcr, 0, 65535), // TEXT MySQL = 65535 max
        ]);

        $this->logEtape($facture, 'ocr', 'succes',
            'OCR extrait — ' . mb_strlen($texteOcr) . ' caractères',
            $this->msDepuis($debut)
        );

        // 3. Classification + Extraction (GPT-4o function calling)
        $extraction = $this->classifierEtExtraire($texteOcr, $facture->id, $facture->tenant_id);

        $extraits = $extraction['donnees_extraites'] ?? [];

        $facture->update([
            'statut'                           => 'a_valider',
            'type_document'                    => $extraction['type_document'] ?? null,
            'sous_type'                        => $extraction['sous_type'] ?? null,
            'score_confiance_classification'   => $extraction['score_confiance'] ?? null,
            'justification_ia'                 => $extraction['justification'] ?? null,
            'donnees_extraites'                => $extraits,
            'n8n_finished_at'                  => now(),
            // Dénormalisation pour recherche rapide
            'numero_facture'                   => $extraits['numero_facture'] ?? null,
            'fournisseur_client'               => $extraits['fournisseur'] ?? $extraits['client'] ?? null,
            'ifu_tiers'                        => $extraits['ifu'] ?? null,
            'code_mecef'                       => $extraits['code_mecef'] ?? null,
            'date_facture'                     => $this->parseDate($extraits['date_facture'] ?? null),
            'montant_ht'                       => $extraits['montant_ht'] ?? null,
            'montant_tva'                      => $extraits['montant_tva'] ?? null,
            'montant_ttc'                      => $extraits['montant_ttc'] ?? null,
            'montant_aib'                      => $extraits['montant_aib'] ?? null,
            'montant_rirf'                     => $extraits['montant_rirf'] ?? null,
            'regime_fiscal'                    => $extraits['regime'] ?? null,
            'mode_paiement'                    => $extraits['mode_paiement'] ?? null,
        ]);

        $this->logEtape($facture, 'extraction', 'succes',
            "Type: {$extraction['type_document']} — Confiance: " . ($extraction['score_confiance'] ?? '?') . "%",
            $this->msDepuis($debut)
        );

        // 4. Génération des écritures SYSCOHADA
        $this->syscohada->genererEcrituresManuel($facture->fresh());

        $this->logEtape($facture, 'generation_ecritures', 'succes',
            'Écritures SYSCOHADA générées',
            $this->msDepuis($debut)
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // Étape 1 — Lecture du fichier
    // ─────────────────────────────────────────────────────────────────────

    private function lireFichier(Facture $facture): string
    {
        $path = $facture->pdf_path;

        // Les PDFs sont stockés dans storage/app/private/ (jamais accessibles publiquement)
        // Le pdf_path stocké en BDD ne contient PAS le préfixe "private/"
        $fullPath = "private/{$path}";

        if (!$path || !Storage::disk('local')->exists($fullPath)) {
            throw new \RuntimeException("Fichier introuvable : {$fullPath}");
        }

        return Storage::disk('local')->get($fullPath);
    }

    private function detecterMime(string $nomFichier): string
    {
        $ext = strtolower(pathinfo($nomFichier, PATHINFO_EXTENSION));

        return match($ext) {
            'pdf'        => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png'        => 'image/png',
            'webp'       => 'image/webp',
            default      => 'application/pdf',
        };
    }

    // ─────────────────────────────────────────────────────────────────────
    // Étape 2 — OCR (Mistral OCR ou Mistral Vision)
    // ─────────────────────────────────────────────────────────────────────

    private function ocr(string $contenu, string $mime, string $factureId): string
    {
        if ($mime === 'application/pdf') {
            return $this->ocrPdf($contenu, $factureId);
        }
        return $this->ocrImage($contenu, $mime, $factureId);
    }

    /**
     * OCR PDF via Mistral OCR API (mistral-ocr-latest).
     * Retourne le texte Markdown extrait de toutes les pages.
     */
    private function ocrPdf(string $pdfBinaire, string $factureId): string
    {
        if (empty($this->mistralKey)) {
            throw new \RuntimeException('MISTRAL_API_KEY non configurée');
        }

        $base64 = base64_encode($pdfBinaire);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->mistralKey,
            'Content-Type'  => 'application/json',
        ])
        ->timeout(60)
        ->post('https://api.mistral.ai/v1/ocr', [
            'model'    => $this->ocrModel,
            'document' => [
                'type'         => 'document_url',
                'document_url' => 'data:application/pdf;base64,' . $base64,
            ],
        ]);

        if (!$response->successful()) {
            Log::error('TraitementIA: Mistral OCR failed', [
                'facture_id' => $factureId,
                'status'     => $response->status(),
                'body'       => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException("Mistral OCR: HTTP {$response->status()}");
        }

        $data  = $response->json();
        $pages = $data['pages'] ?? [];
        $texte = '';

        foreach ($pages as $page) {
            $texte .= ($page['markdown'] ?? '') . "\n\n";
        }

        if (empty(trim($texte))) {
            throw new \RuntimeException('Mistral OCR: aucun texte extrait (document vide ou illisible)');
        }

        return $texte;
    }

    /**
     * OCR image via Mistral Vision (pixtral-large-latest).
     * Supporte JPG, PNG, WEBP.
     */
    private function ocrImage(string $imageContenu, string $mime, string $factureId): string
    {
        if (empty($this->mistralKey)) {
            throw new \RuntimeException('MISTRAL_API_KEY non configurée');
        }

        $base64   = base64_encode($imageContenu);
        $dataUrl  = "data:{$mime};base64,{$base64}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->mistralKey,
            'Content-Type'  => 'application/json',
        ])
        ->timeout(60)
        ->post('https://api.mistral.ai/v1/chat/completions', [
            'model' => $this->visionModel,
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => [
                        [
                            'type'      => 'image_url',
                            'image_url' => ['url' => $dataUrl],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Extrais tout le texte de cette image de facture. '
                                    . 'Retourne le contenu complet sans reformuler, '
                                    . 'en préservant les numéros, montants et dates.',
                        ],
                    ],
                ],
            ],
        ]);

        if (!$response->successful()) {
            Log::error('TraitementIA: Mistral Vision failed', [
                'facture_id' => $factureId,
                'status'     => $response->status(),
                'body'       => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException("Mistral Vision: HTTP {$response->status()}");
        }

        $texte = $response->json('choices.0.message.content', '');

        if (empty(trim($texte))) {
            throw new \RuntimeException('Mistral Vision: aucun texte extrait');
        }

        return $texte;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Étape 3 — Classification + Extraction (GPT-4o function calling)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Envoie le texte OCR à GPT-4o via function calling.
     * Retourne un tableau structuré avec type_document, données extraites.
     */
    private function classifierEtExtraire(string $texteOcr, string $factureId, ?string $tenantId = null): array
    {
        if (empty($this->openaiKey)) {
            throw new \RuntimeException('OPENAI_API_KEY non configurée');
        }

        $systemPrompt = $this->construireSystemPrompt($tenantId);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiKey,
            'Content-Type'  => 'application/json',
        ])
        ->timeout(90)
        ->post('https://api.openai.com/v1/chat/completions', [
            'model'       => $this->gptModel,
            'temperature' => 0,   // Zéro = déterministe, essentiel pour la compta
            'tool_choice' => ['type' => 'function', 'function' => ['name' => 'classifier_et_extraire']],
            'tools'       => [$this->schemaFunctionCalling()],
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => "Voici le texte extrait de la facture :\n\n---\n{$texteOcr}\n---"],
            ],
        ]);

        if (!$response->successful()) {
            Log::error('TraitementIA: GPT-4o failed', [
                'facture_id' => $factureId,
                'status'     => $response->status(),
                'body'       => substr($response->body(), 0, 500),
            ]);
            throw new \RuntimeException("GPT-4o: HTTP {$response->status()}");
        }

        // Extraire les arguments de la function call
        $toolCall  = $response->json('choices.0.message.tool_calls.0');
        $arguments = $toolCall['function']['arguments'] ?? '{}';

        $resultat  = json_decode($arguments, true);
        if (!$resultat) {
            throw new \RuntimeException('GPT-4o: réponse JSON invalide — ' . substr($arguments, 0, 200));
        }

        // Reconstituer dans le format attendu par N8nCallbackController / update facture
        return [
            'type_document'    => strtoupper($resultat['type_document'] ?? 'ACHAT'),
            'sous_type'        => $resultat['sous_type'] ?? null,
            'score_confiance'  => (int) ($resultat['score_confiance'] ?? 50),
            'justification'    => $resultat['justification'] ?? null,
            'donnees_extraites' => [
                'numero_facture'   => $resultat['numero_facture'] ?? null,
                'fournisseur'      => $resultat['fournisseur'] ?? null,
                'client'           => $resultat['client'] ?? null,
                'ifu'              => $resultat['ifu'] ?? null,
                'code_mecef'       => $resultat['code_mecef'] ?? null,
                'date_facture'     => $resultat['date_facture'] ?? null,
                'montant_ht'       => isset($resultat['montant_ht']) ? (float) $resultat['montant_ht'] : null,
                'montant_tva'      => isset($resultat['montant_tva']) ? (float) $resultat['montant_tva'] : null,
                'montant_ttc'      => isset($resultat['montant_ttc']) ? (float) $resultat['montant_ttc'] : null,
                'montant_aib'      => isset($resultat['montant_aib']) ? (float) $resultat['montant_aib'] : null,
                'montant_rirf'     => isset($resultat['montant_rirf']) ? (float) $resultat['montant_rirf'] : null,
                'regime'           => $resultat['regime_fiscal'] ?? null,
                'mode_paiement'    => $resultat['mode_paiement'] ?? null,
                'compte_syscohada' => $resultat['compte_syscohada'] ?? null,
                'libelle_compte'   => $resultat['libelle_compte'] ?? null,
            ],
        ];
    }

    /**
     * Schéma OpenAI function calling pour l'extraction SYSCOHADA Bénin.
     */
    private function schemaFunctionCalling(): array
    {
        return [
            'type'     => 'function',
            'function' => [
                'name'        => 'classifier_et_extraire',
                'description' => 'Classifie un document comptable et extrait les données fiscales SYSCOHADA (Bénin — régimes B et D, AIB 1%, RIRF).',
                'parameters'  => [
                    'type'       => 'object',
                    'required'   => ['type_document', 'score_confiance'],
                    'properties' => [

                        // ── Classification ──────────────────────────────────
                        'type_document' => [
                            'type' => 'string',
                            'enum' => ['VENTE', 'ACHAT', 'CHARGE', 'NOTE_FRAIS', 'RELEVE_BANCAIRE', 'AUTRE'],
                            'description' => 'Type principal : VENTE (facture émise), ACHAT (facture reçue marchandises/matières), CHARGE (facture reçue services/loyers/honoraires), NOTE_FRAIS, RELEVE_BANCAIRE.',
                        ],
                        'sous_type' => [
                            'type'        => 'string',
                            'description' => 'Précision optionnelle : LOYER, TELEPHONE, TRANSPORT, SALAIRE, HONORAIRES, etc.',
                        ],
                        'score_confiance' => [
                            'type'        => 'integer',
                            'minimum'     => 0,
                            'maximum'     => 100,
                            'description' => 'Niveau de confiance de la classification (0-100).',
                        ],
                        'justification' => [
                            'type'        => 'string',
                            'description' => 'Justification courte (1 phrase) du type choisi.',
                        ],

                        // ── Identification du document ───────────────────
                        'numero_facture' => [
                            'type'        => 'string',
                            'description' => 'Numéro de la facture tel qu\'il apparaît sur le document.',
                        ],
                        'fournisseur' => [
                            'type'        => 'string',
                            'description' => 'Nom du fournisseur (pour ACHAT/CHARGE).',
                        ],
                        'client' => [
                            'type'        => 'string',
                            'description' => 'Nom du client (pour VENTE).',
                        ],
                        'ifu' => [
                            'type'        => 'string',
                            'description' => 'IFU (Identifiant Fiscal Unique) Bénin — 13 chiffres.',
                        ],
                        'code_mecef' => [
                            'type'        => 'string',
                            'description' => 'Code MeceF (Machine Électronique de Contrôle et de Facturation) — code alphanumérique sur la facture normalisée.',
                        ],
                        'date_facture' => [
                            'type'        => 'string',
                            'description' => 'Date de la facture au format YYYY-MM-DD.',
                        ],

                        // ── Montants ─────────────────────────────────────
                        'montant_ht' => [
                            'type'        => 'number',
                            'description' => 'Montant Hors Taxes en FCFA.',
                        ],
                        'montant_tva' => [
                            'type'        => 'number',
                            'description' => 'Montant TVA (18% régime B) en FCFA. 0 si régime D (exonéré).',
                        ],
                        'montant_ttc' => [
                            'type'        => 'number',
                            'description' => 'Montant Toutes Taxes Comprises en FCFA.',
                        ],
                        'montant_aib' => [
                            'type'        => 'number',
                            'description' => 'AIB (Acompte sur IS à la Base) 1% du TTC, retenu à la source. 0 si non applicable.',
                        ],
                        'montant_rirf' => [
                            'type'        => 'number',
                            'description' => 'RIRF (Retenue IR sur Facture) — retenu par le client sur prestation de services.',
                        ],

                        // ── Fiscalité et paiement ────────────────────────
                        'regime_fiscal' => [
                            'type' => 'string',
                            'enum' => ['B', 'D', 'A', 'inconnu'],
                            'description' => 'Régime fiscal Bénin : B = assujetti TVA 18%, D = exonéré, A = forfaitaire.',
                        ],
                        'mode_paiement' => [
                            'type' => 'string',
                            'enum' => ['especes', 'virement', 'cheque', 'mobile_money', 'autre'],
                            'description' => 'Mode de règlement. mobile_money = MTN MoMo / Moov Money.',
                        ],

                        // ── Compte SYSCOHADA (charges uniquement) ────────
                        'compte_syscohada' => [
                            'type'        => 'string',
                            'description' => 'Numéro de compte SYSCOHADA pour les CHARGES (ex: 6222 Loyer, 6281 Téléphone, 6324 Honoraires). Laisser vide pour VENTE/ACHAT.',
                        ],
                        'libelle_compte' => [
                            'type'        => 'string',
                            'description' => 'Libellé SYSCOHADA correspondant au compte (ex: "Locations de bâtiments").',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * System prompt riche pour guider GPT-4o sur les spécificités SYSCOHADA Bénin.
     */
    private function construireSystemPrompt(?string $tenantId = null): string
    {
        $prompt = <<<PROMPT
Tu es un expert-comptable SYSCOHADA Révisé spécialisé en fiscalité béninoise.
Tu analyses des documents comptables (factures, notes de frais, relevés) et les classifies selon le Plan Comptable SYSCOHADA Révisé 2017.

RÈGLES DE CLASSIFICATION :
- VENTE : facture que notre entreprise a émise (client = nous, fournisseur = notre entreprise)
- ACHAT : facture reçue pour des marchandises ou matières premières (compte classe 6011/6021)
- CHARGE : facture reçue pour des services, loyers, honoraires, téléphone, eau, électricité
- NOTE_FRAIS : justificatif de dépenses du personnel
- RELEVE_BANCAIRE : extrait de compte bancaire ou mobile money

FISCALITÉ BÉNIN :
- TVA : 18% (régime B = assujetti, régime D = exonéré)
- AIB : 1% du TTC, retenu à la source par les grandes entreprises (DGI)
- RIRF : retenue sur prestation de services (variable selon contrat)
- MeceF : machine de facturation normalisée — code alphanumérique obligatoire depuis 2022

MONTANTS :
- TTC = HT × 1.18 (régime B) ou TTC = HT (régime D)
- Les montants doivent être en FCFA (valeur numérique sans séparateur)
- Si un montant est illisible, ne pas l'inventer — laisser null

COMPTE SYSCOHADA pour les CHARGES (exemples) :
- 6011 Achats marchandises dans la Région
- 6222 Locations de bâtiments (loyer)
- 6242 Entretien biens mobiliers
- 6251 Assurances multirisques
- 6271 Publicité
- 6281 Frais de téléphone
- 6317 Frais mobile money
- 6318 Autres frais bancaires
- 6324 Honoraires professions réglementées
- 6411 Impôts fonciers
- 6611 Salaires
- 6051 Eau / 6052 Électricité

DATE : format YYYY-MM-DD obligatoire. Si la date n'est pas clairement lisible, mettre null.

Appelle la fonction classifier_et_extraire avec toutes les informations disponibles.
PROMPT;

        // Conventions propres au cabinet, déduites d'un modèle d'export qu'il a fourni
        // (voir ModeleExportService) — prioritaires sur les exemples génériques ci-dessus
        // quand elles précisent la classification ou la numérotation des comptes.
        $notesStyle = $tenantId
            ? ModeleExport::where('tenant_id', $tenantId)->value('notes_style')
            : null;

        if ($notesStyle) {
            $prompt .= "\n\nCONVENTIONS PROPRES À CE CABINET (à respecter en priorité) :\n{$notesStyle}";
        }

        return $prompt;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    private function parseDate(?string $date): ?string
    {
        if (!$date) return null;
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function logEtape(Facture $facture, string $etape, string $statut, string $message, int $dureeMs = 0): void
    {
        TraitementLog::create([
            'facture_id' => $facture->id,
            'etape'      => $etape,
            'statut'     => $statut,
            'duree_ms'   => $dureeMs,
            'message'    => $message,
        ]);
    }

    private function msDepuis(float $debut): int
    {
        return (int) ((microtime(true) - $debut) * 1000);
    }
}
