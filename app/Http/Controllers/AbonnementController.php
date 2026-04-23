<?php

namespace App\Http\Controllers;

use App\Mail\PaiementConfirmeMail;
use App\Models\Abonnement;
use App\Models\Plan;
use App\Services\FeexPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AbonnementController extends Controller
{
    public function __construct(private FeexPayService $feexpay) {}

    /**
     * Page des plans d'abonnement.
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $plans  = Plan::actifs();
        $abonnementActif = $tenant->abonnementActif();
        $historiqueAbonnements = Abonnement::where('tenant_id', $tenant->id)
            ->latest()
            ->take(10)
            ->get();

        return view('abonnement.index', compact('tenant', 'plans', 'abonnementActif', 'historiqueAbonnements'));
    }

    /**
     * Initie un paiement FeexPay et redirige vers la page de paiement mobile money.
     */
    public function initierPaiement(Request $request)
    {
        $request->validate([
            'plan_slug' => 'required|exists:plans,slug',
        ]);

        $tenant = auth()->user()->tenant;
        $plan   = Plan::where('slug', $request->plan_slug)->where('actif', true)->firstOrFail();

        if ($plan->prix_mensuel_xof === 0) {
            return back()->withErrors(['plan' => 'Le plan Trial est gratuit et activé automatiquement.']);
        }

        $result = $this->feexpay->creerTransaction(
            montantXof:  $plan->prix_mensuel_xof,
            description: "ComptaSaaS — Abonnement {$plan->nom} (1 mois)",
            tenantId:    $tenant->id,
            planSlug:    $plan->slug,
            redirectUrl: route('abonnement.succes'),
            cancelUrl:   route('abonnement.index')
        );

        if (!$result['succes']) {
            return back()->withErrors([
                'paiement' => 'Erreur lors de la création du paiement : ' . ($result['erreur'] ?? 'Erreur inconnue'),
            ]);
        }

        // Rediriger vers la page FeexPay (mobile money MTN/Moov)
        return redirect($result['payment_url']);
    }

    /**
     * Page de confirmation après paiement réussi.
     */
    public function succes(Request $request)
    {
        $user   = auth()->user();
        $tenant = $user->tenant;
        $abonnement = Abonnement::with(['plan', 'tenant'])
            ->where('tenant_id', $tenant->id)
            ->where('statut', 'actif')
            ->latest()
            ->first();

        // Email de confirmation de paiement
        if ($abonnement) {
            try {
                Mail::to($user->email)->send(new PaiementConfirmeMail($user, $abonnement));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Email paiement non envoyé : ' . $e->getMessage());
            }
        }

        return view('abonnement.succes', compact('tenant', 'abonnement'));
    }
}
