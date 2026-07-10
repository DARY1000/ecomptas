<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Plan;
use App\Models\Tenant;
use App\Services\FeexPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Reçoit les confirmations de paiement FeexPay.
 * Active ou renouvelle l'abonnement du cabinet après paiement mobile money.
 * Route exclue du CSRF dans bootstrap/app.php.
 *
 * Le webhook FeexPay n'est pas signé cryptographiquement (voir doc "Sécurité" —
 * aucune vérification de signature documentée). On ne fait donc jamais confiance
 * au statut/montant du payload seul : on revérifie auprès de l'API FeexPay avant
 * d'activer quoi que ce soit.
 */
class FeexPayController extends Controller
{
    public function __construct(private FeexPayService $feexpay) {}

    public function callback(Request $request)
    {
        // Payload réel FeexPay (doc webhook) : reference, order_id, status, amount, callback_info...
        $montant     = (int) $request->input('amount', 0);
        $transId     = $request->input('reference') ?? $request->input('order_id');
        $callbackRaw = $request->input('callback_info') ?: '{}';

        if (!$transId) {
            Log::error('FeexPay webhook: reference manquante', $request->all());
            return response()->json(['error' => 'reference manquante'], 400);
        }

        // Revérification serveur-à-serveur — ne jamais se fier uniquement au payload reçu.
        $transaction = $this->feexpay->verifierTransaction($transId);
        $statut      = $transaction['status'] ?? null;
        $montant     = (int) ($transaction['amount'] ?? $montant);

        Log::info('FeexPay webhook reçu', [
            'status'  => $statut,
            'montant' => $montant,
            'trans_id' => $transId,
        ]);

        // Ignorer les paiements non réussis (ou non confirmables) sans erreur
        if ($statut !== 'SUCCESSFUL') {
            Log::info('FeexPay: paiement non abouti ou invérifiable', ['status' => $statut, 'trans_id' => $transId]);
            return response()->json(['ok' => true]);
        }

        // Décoder les infos retournées lors de la création de la transaction
        $callbackInfo = json_decode($callbackRaw, true);
        $tenantId = $callbackInfo['tenant_id'] ?? null;
        $planSlug = $callbackInfo['plan'] ?? null;

        if (!$tenantId || !$planSlug) {
            Log::error('FeexPay callback: tenant_id ou plan manquant', ['callback_info' => $callbackRaw]);
            return response()->json(['error' => 'Données manquantes'], 400);
        }

        $tenant = Tenant::find($tenantId);
        $plan   = Plan::where('slug', $planSlug)->first();

        if (!$tenant || !$plan) {
            Log::error('FeexPay callback: tenant ou plan introuvable', [
                'tenant_id' => $tenantId,
                'plan'      => $planSlug,
            ]);
            return response()->json(['error' => 'Tenant ou plan introuvable'], 404);
        }

        // Le montant réellement payé (vérifié auprès de FeexPay) doit correspondre au prix du plan —
        // sinon quelqu'un pourrait payer un petit montant et le faire passer pour un plan plus cher.
        if ($montant < $plan->prix_mensuel_xof) {
            Log::error('FeexPay callback: montant payé insuffisant pour le plan demandé', [
                'tenant_id'    => $tenantId,
                'plan'         => $planSlug,
                'montant_paye' => $montant,
                'prix_plan'    => $plan->prix_mensuel_xof,
            ]);
            return response()->json(['error' => 'Montant insuffisant pour ce plan'], 400);
        }

        // Éviter les doublons (idempotence)
        if ($transId && Abonnement::where('transaction_id', $transId)->exists()) {
            Log::info('FeexPay: transaction déjà traitée', ['transaction_id' => $transId]);
            return response()->json(['ok' => true]);
        }

        // Créer l'enregistrement d'abonnement
        Abonnement::create([
            'tenant_id'           => $tenant->id,
            'plan_id'             => $plan->id,
            'statut'              => 'actif',
            'processeur_paiement' => 'feexpay',
            'transaction_id'      => $transId,
            'montant_xof'         => $montant,
            'debut_le'            => now(),
            'expire_le'           => now()->addDays(30),
            'metadata_paiement'   => $request->all(),
        ]);

        // Mettre à jour le tenant
        $tenant->update([
            'plan'                   => $plan->slug,
            'statut'                 => 'actif',
            'quota_factures_mensuel' => $plan->quota_factures,
            'quota_users'            => $plan->quota_users,
            'abonnement_expire_le'   => now()->addDays(30),
        ]);

        Log::info("FeexPay: abonnement activé — {$tenant->nom} → {$plan->nom}");

        return response()->json(['ok' => true]);
    }
}
