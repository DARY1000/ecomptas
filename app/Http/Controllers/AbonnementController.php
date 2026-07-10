<?php

namespace App\Http\Controllers;

use App\Mail\PaiementConfirmeMail;
use App\Models\Abonnement;
use App\Models\Plan;
use App\Services\FeexPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AbonnementController extends Controller
{
    public function __construct(private FeexPayService $feexpay) {}

    /**
     * Page des plans d'abonnement. Si ?payer=<slug> est présent (redirection depuis
     * initierPaiement), la vue ouvre automatiquement le widget FeexPay pour ce plan.
     */
    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $plans  = Plan::actifs();
        $abonnementActif = $tenant->abonnementActif();
        $historiqueAbonnements = Abonnement::where('tenant_id', $tenant->id)
            ->latest()
            ->take(10)
            ->get();

        $planAPayer = $request->query('payer')
            ? $plans->firstWhere('slug', $request->query('payer'))
            : null;

        return view('abonnement.index', compact(
            'tenant', 'plans', 'abonnementActif', 'historiqueAbonnements', 'planAPayer'
        ));
    }

    /**
     * Marque le plan choisi comme "en cours de paiement" (session) et redirige vers
     * la page d'abonnement, qui ouvrira automatiquement le widget de paiement FeexPay.
     * Le paiement lui-même se fait entièrement côté client via leur modale hébergée —
     * pas d'appel serveur-à-serveur pour "créer" une transaction (leur API ne le propose pas).
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

        // Mémorise le plan choisi pour que abonnement.succes sache quoi activer au retour
        // (FeexPay n'ajoute que la référence de transaction à l'URL de callback, pas nos
        // métadonnées métier).
        session(['feexpay_plan_en_cours' => $plan->slug]);

        return redirect()->route('abonnement.index', ['payer' => $plan->slug]);
    }

    /**
     * Page de retour après paiement (callback_url du widget FeexPay, avec ?ref=<reference>
     * ajouté automatiquement par FeexPay). Revérifie le paiement serveur-à-serveur avant
     * d'activer quoi que ce soit — jamais confiance dans le simple fait d'atterrir ici.
     */
    public function succes(Request $request)
    {
        $user   = auth()->user();
        $tenant = $user->tenant;

        $reference = $request->query('ref');
        $planSlug  = session('feexpay_plan_en_cours');
        $plan      = $planSlug ? Plan::where('slug', $planSlug)->first() : null;

        $abonnement = null;
        $erreur     = null;

        if (!$reference || !$plan) {
            $erreur = "Impossible d'identifier le paiement. Si le montant a bien été débité, contactez le support.";
        } else {
            $transaction = $this->feexpay->verifierTransaction($reference);
            $statut      = $transaction['status'] ?? null;
            $montant     = (int) ($transaction['amount'] ?? 0);

            if ($statut !== 'SUCCESSFUL') {
                $erreur = "Le paiement n'a pas été confirmé (statut : {$statut}). Si le débit a eu lieu, contactez le support.";
            } elseif ($montant < $plan->prix_mensuel_xof) {
                Log::error('FeexPay succes: montant insuffisant', [
                    'tenant_id' => $tenant->id, 'plan' => $plan->slug,
                    'montant_paye' => $montant, 'prix_plan' => $plan->prix_mensuel_xof,
                ]);
                $erreur = 'Montant payé insuffisant pour ce plan. Contactez le support.';
            } else {
                $abonnement = Abonnement::activerPourPaiement($tenant, $plan, $reference, $montant, $transaction);
                session()->forget('feexpay_plan_en_cours');

                try {
                    Mail::to($user->email)->send(new PaiementConfirmeMail($user, $abonnement));
                } catch (\Throwable $e) {
                    Log::warning('Email paiement non envoyé : ' . $e->getMessage());
                }
            }
        }

        return view('abonnement.succes', compact('tenant', 'abonnement', 'erreur'));
    }
}
