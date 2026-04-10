<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Reçoit les confirmations de paiement FeexPay.
 * Active ou renouvelle l'abonnement du cabinet après paiement mobile money.
 * Route exclue du CSRF dans bootstrap/app.php.
 */
class FeexPayController extends Controller
{
    public function callback(Request $request)
    {
        $statut      = $request->input('status');
        $montant     = (int) $request->input('amount', 0);
        $transId     = $request->input('id') ?? $request->input('transaction_id');
        $callbackRaw = $request->input('callback_info', '{}');

        Log::info('FeexPay webhook reçu', [
            'status'  => $statut,
            'montant' => $montant,
            'trans_id' => $transId,
        ]);

        // Ignorer les paiements non réussis sans erreur
        if ($statut !== 'SUCCESSFUL') {
            Log::info('FeexPay: paiement non abouti', ['status' => $statut]);
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
