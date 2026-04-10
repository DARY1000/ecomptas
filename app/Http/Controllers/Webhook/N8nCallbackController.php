<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\TraitementLog;
use App\Services\N8nService;
use App\Services\SYSCOHADAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Reçoit les résultats du pipeline n8n.cloud (OCR + Classification + Extraction).
 * Sécurisé par signature HMAC-SHA256.
 * Route exclue du CSRF dans bootstrap/app.php.
 */
class N8nCallbackController extends Controller
{
    public function __construct(
        private N8nService       $n8n,
        private SYSCOHADAService $syscohada
    ) {}

    /**
     * Point d'entrée unique pour tous les callbacks n8n.
     * n8n.cloud → POST /webhooks/n8n/callback
     */
    public function recevoir(Request $request)
    {
        // RÈGLE 2 — Vérification signature HMAC obligatoire
        $signature = $request->header('X-N8N-Secret', '');
        $payload   = $request->getContent();

        if (!$this->n8n->verifierSignature($payload, $signature)) {
            Log::warning('N8n callback: signature HMAC invalide', [
                'ip'        => $request->ip(),
                'signature' => substr($signature, 0, 10) . '...',
            ]);
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $data      = $request->json()->all();
        $factureId = $data['facture_id'] ?? null;
        $etape     = $data['etape'] ?? null;

        if (!$factureId || !$etape) {
            return response()->json(['error' => 'Champs manquants : facture_id et etape requis.'], 400);
        }

        // RÈGLE 1 — Récupérer la facture (isolation tenant vérifiée via tenant_id du payload)
        $facture = Facture::find($factureId);
        if (!$facture) {
            return response()->json(['error' => 'Facture introuvable'], 404);
        }

        // Vérifier que le tenant_id correspond (protection supplémentaire)
        if ($data['tenant_id'] && $facture->tenant_id !== $data['tenant_id']) {
            Log::warning('N8n callback: tenant_id mismatch', [
                'facture_tenant' => $facture->tenant_id,
                'payload_tenant' => $data['tenant_id'],
            ]);
            return response()->json(['error' => 'Tenant mismatch'], 403);
        }

        try {
            $this->traiterEtape($facture, $etape, $data);
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('N8n callback: erreur lors du traitement', [
                'facture_id' => $factureId,
                'etape'      => $etape,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);
            $facture->update(['statut' => 'erreur']);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function traiterEtape(Facture $facture, string $etape, array $data): void
    {
        $dureeMs = $data['duree_ms'] ?? null;

        match($etape) {
            'extraction_terminee' => $this->traiterExtraction($facture, $data, $dureeMs),
            'ecritures_generees'  => $this->traiterEcritures($facture, $data, $dureeMs),
            'erreur'              => $this->traiterErreur($facture, $data),
            default               => Log::info("N8n callback: étape '{$etape}' reçue (ignorée)"),
        };
    }

    /**
     * Traite le résultat de l'extraction (OCR + Classification + Extraction).
     * C'est l'étape principale — met la facture en statut "à valider".
     */
    private function traiterExtraction(Facture $facture, array $data, ?int $dureeMs): void
    {
        $extraits = $data['donnees_extraites'] ?? [];

        $facture->update([
            'statut'             => 'a_valider',
            'type_document'      => $data['type_document'] ?? null,
            'sous_type'          => $data['sous_type'] ?? null,
            'score_confiance_classification' => $data['score_confiance'] ?? null,
            'justification_ia'   => $data['justification'] ?? null,
            'donnees_extraites'  => $extraits,
            'n8n_finished_at'    => now(),
            // Dénormalisation pour recherche rapide
            'numero_facture'     => $extraits['numero_facture'] ?? null,
            'fournisseur_client' => $extraits['fournisseur'] ?? $extraits['client'] ?? null,
            'ifu_tiers'          => $extraits['ifu'] ?? null,
            'code_mecef'         => $extraits['code_mecef'] ?? null,
            'date_facture'       => $extraits['date_facture'] ?? null,
            'montant_ht'         => $extraits['montant_ht'] ?? null,
            'montant_tva'        => $extraits['montant_tva'] ?? null,
            'montant_ttc'        => $extraits['montant_ttc'] ?? null,
            'montant_aib'        => $extraits['montant_aib'] ?? null,
            'montant_rirf'       => $extraits['montant_rirf'] ?? null,
            'regime_fiscal'      => $extraits['regime'] ?? null,
            'mode_paiement'      => $extraits['mode_paiement'] ?? null,
        ]);

        // Générer les écritures SYSCOHADA
        if (!empty($data['ecritures'])) {
            // n8n a fourni les écritures directement
            $this->syscohada->genererEcritures($facture, $data['ecritures']);
        } else {
            // Fallback : génération locale selon les règles SYSCOHADA Bénin
            $this->syscohada->genererEcrituresManuel($facture->fresh());
        }

        TraitementLog::create([
            'facture_id' => $facture->id,
            'etape'      => 'extraction',
            'statut'     => 'succes',
            'duree_ms'   => $dureeMs,
            'message'    => "Type: {$facture->type_document} — Confiance: " . ($data['score_confiance'] ?? '?') . "%",
        ]);
    }

    private function traiterEcritures(Facture $facture, array $data, ?int $dureeMs): void
    {
        if (!empty($data['ecritures'])) {
            $this->syscohada->genererEcritures($facture, $data['ecritures']);
        }

        TraitementLog::create([
            'facture_id' => $facture->id,
            'etape'      => 'generation_ecritures',
            'statut'     => 'succes',
            'duree_ms'   => $dureeMs,
            'message'    => count($data['ecritures'] ?? []) . ' écritures SYSCOHADA générées',
        ]);
    }

    private function traiterErreur(Facture $facture, array $data): void
    {
        $facture->update(['statut' => 'erreur']);

        TraitementLog::create([
            'facture_id' => $facture->id,
            'etape'      => 'extraction',
            'statut'     => 'erreur',
            'message'    => $data['message_erreur'] ?? 'Erreur pipeline n8n.cloud',
        ]);
    }
}
