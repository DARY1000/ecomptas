<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFactureRequest;
use App\Jobs\TraiterFacturePDF;
use App\Models\Facture;
use App\Services\SYSCOHADAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FactureController extends Controller
{
    public function __construct(private SYSCOHADAService $syscohada) {}

    /**
     * Liste des factures du cabinet avec filtres.
     */
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $factures = Facture::where('tenant_id', $tenantId)
            ->when($request->type, fn($q) => $q->where('type_document', $request->type))
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->q, fn($q) =>
                $q->where(fn($sq) => $sq
                    ->where('numero_facture', 'like', '%' . $request->q . '%')
                    ->orWhere('fournisseur_client', 'like', '%' . $request->q . '%')
                )
            )
            ->when($request->date_debut, fn($q) => $q->whereDate('date_facture', '>=', $request->date_debut))
            ->when($request->date_fin,   fn($q) => $q->whereDate('date_facture', '<=', $request->date_fin))
            ->with('uploadePar:id,name')
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('factures.index', compact('factures'));
    }

    /**
     * Formulaire d'upload.
     */
    public function upload()
    {
        return view('factures.upload');
    }

    /**
     * Reçoit les PDF, les stocke localement, dispatch les jobs de traitement IA.
     */
    public function store(UploadFactureRequest $request)
    {
        $user   = auth()->user();
        $tenant = $user->tenant;

        $factures = [];

        foreach ($request->file('pdfs') as $fichier) {
            $nomFichier = Str::uuid() . '.pdf';
            // Stockage local Hostinger — storage/app/private/tenants/{id}/pdfs/
            // RÈGLE 3 : jamais de chemin public direct
            $chemin = "tenants/{$tenant->id}/pdfs/{$nomFichier}";

            Storage::disk('local')->put(
                "private/{$chemin}",
                file_get_contents($fichier->getRealPath())
            );

            $facture = Facture::create([
                'tenant_id'        => $tenant->id,
                'uploaded_by'      => $user->id,
                'statut'           => 'uploade',
                'pdf_path'         => $chemin,
                'pdf_nom_original' => $fichier->getClientOriginalName(),
                'pdf_taille_bytes' => $fichier->getSize(),
            ]);

            // Dispatch vers la queue database — exécuté par le cron Hostinger
            TraiterFacturePDF::dispatch($facture)->onQueue('default');

            $factures[] = $facture;
        }

        $msg = count($factures) === 1
            ? 'Facture uploadée — traitement IA en cours (mise à jour auto dans quelques minutes).'
            : count($factures) . ' factures uploadées — traitement IA en cours.';

        return redirect()->route('factures.index')->with('succes', $msg);
    }

    /**
     * Détail d'une facture avec ses écritures et logs.
     */
    public function show(Facture $facture)
    {
        $this->autoriserTenant($facture);
        $facture->load(['ecritures', 'logs', 'uploadePar:id,name', 'validePar:id,name']);

        return view('factures.show', compact('facture'));
    }

    /**
     * Route protégée pour servir le PDF à n8n.cloud via token signé.
     * RÈGLE 3 : les PDFs ne sont jamais accessibles directement.
     */
    public function servirPdf(Request $request, Facture $facture)
    {
        try {
            $data = decrypt($request->query('token'));
            abort_unless($data['facture_id'] === $facture->id, 403);
            abort_unless($data['expires_at'] >= now()->timestamp, 410, 'Token expiré.');
        } catch (\Exception $e) {
            abort(403, 'Token invalide.');
        }

        $chemin = "private/{$facture->pdf_path}";
        abort_unless(Storage::disk('local')->exists($chemin), 404);

        return response()->streamDownload(
            fn () => print(Storage::disk('local')->get($chemin)),
            $facture->pdf_nom_original,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Formulaire de révision / correction des données extraites.
     */
    public function revue(Facture $facture)
    {
        $this->autoriserTenant($facture);
        abort_unless($facture->statut === 'a_valider', 404);

        $facture->load('ecritures');

        return view('factures.revue', compact('facture'));
    }

    /**
     * Endpoint AJAX — retourne le statut courant (pour le polling JS).
     */
    public function statut(Facture $facture)
    {
        $this->autoriserTenant($facture);

        return response()->json([
            'statut'       => $facture->statut,
            'statut_label' => $facture->statut_label,
            'couleur'      => $facture->statut_couleur,
        ]);
    }

    /**
     * Valide une facture après révision comptable.
     */
    public function valider(Request $request, Facture $facture)
    {
        $this->autoriserTenant($facture);
        abort_unless(auth()->user()->peutValider(), 403);
        abort_unless($facture->statut === 'a_valider', 422);

        // Sauvegarder les corrections du comptable
        $facture->update([
            'statut'             => 'valide',
            'valide_par'         => auth()->id(),
            'valide_le'          => now(),
            'donnees_corrigees'  => $request->donnees_corrigees ?? $facture->donnees_corrigees,
            'numero_facture'     => $request->get('numero_facture', $facture->numero_facture),
            'fournisseur_client' => $request->get('fournisseur_client', $facture->fournisseur_client),
            'date_facture'       => $request->get('date_facture', $facture->date_facture),
            'montant_ttc'        => $request->get('montant_ttc', $facture->montant_ttc),
            'mode_paiement'      => $request->get('mode_paiement', $facture->mode_paiement),
            'type_document'      => $request->get('type_document', $facture->type_document),
        ]);

        // Regénérer les écritures si des données ont été corrigées
        if ($request->has('regenerer_ecritures')) {
            $this->syscohada->genererEcrituresManuel($facture->fresh());
        }

        return redirect()
            ->route('factures.show', $facture)
            ->with('succes', 'Facture validée et écritures comptables enregistrées.');
    }

    /**
     * Rejette une facture avec motif obligatoire.
     */
    public function rejeter(Request $request, Facture $facture)
    {
        $this->autoriserTenant($facture);
        abort_unless(auth()->user()->peutValider(), 403);
        $request->validate(['motif_rejet' => 'required|string|min:10|max:500']);

        $facture->update([
            'statut'      => 'rejete',
            'motif_rejet' => $request->motif_rejet,
            'valide_par'  => auth()->id(),
        ]);

        return redirect()
            ->route('factures.index')
            ->with('info', 'Facture rejetée.');
    }

    /**
     * RÈGLE 1 — Isolation tenant : une facture ne peut être vue que par son cabinet.
     */
    private function autoriserTenant(Facture $facture): void
    {
        abort_unless($facture->tenant_id === auth()->user()->tenant_id, 403);
    }
}
