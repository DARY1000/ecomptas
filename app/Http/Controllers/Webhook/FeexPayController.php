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

        // custom_id envoyé au widget FeexPay au format "tenant_id|plan_slug" (voir
        // abonnement/index.blade.php). FeexPay ne documente pas précisément si ce champ
        // revient tel quel dans callback_info — on reste tolérant sur le format.
        [$tenantId, $planSlug] = array_pad(explode('|', $callbackRaw, 2), 2, null);

        if (!$tenantId || !$planSlug) {
            Log::warning('FeexPay webhook: tenant_id/plan absents du callback_info — activation laissée à abonnement.succes', [
                'callback_info' => $callbackRaw,
                'trans_id'      => $transId,
            ]);
            return response()->json(['ok' => true]);
        }

        $tenant = Tenant::find($tenantId);
        $plan   = Plan::where('slug', $planSlug)->first();

        if (!$tenant || !$plan) {
            Log::warning('FeexPay webhook: tenant ou plan introuvable pour ce custom_id', [
                'tenant_id' => $tenantId,
                'plan'      => $planSlug,
            ]);
            return response()->json(['ok' => true]);
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

        // Abonnement::activerPourPaiement() est idempotent sur transaction_id —
        // pas de risque de doublon si abonnement.succes est déjà passé par là.
        Abonnement::activerPourPaiement($tenant, $plan, $transId, $montant, $request->all());

        Log::info("FeexPay: abonnement activé (webhook) — {$tenant->nom} → {$plan->nom}");

        return response()->json(['ok' => true]);
    }
}
