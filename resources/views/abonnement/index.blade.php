@extends('layouts.app')
@section('title', 'Abonnement')
@section('page-title', 'Abonnement')

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Abonnement</h1>
        <p class="text-gray-500 text-sm mt-1">Gérez votre plan et votre facturation</p>
    </div>

    {{-- Abonnement actuel --}}
    @if($abonnementActif)
    <div class="bg-green-50 border border-green-200 rounded-xl p-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-green-800">Plan {{ $tenant->plan_slug ?? 'actif' }} — Actif</p>
                <p class="text-sm text-green-700">
                    Expire le {{ $abonnementActif->expire_le?->format('d/m/Y') ?? 'N/A' }}
                    @if($abonnementActif->joursRestants() !== null)
                        ({{ $abonnementActif->joursRestants() }} jour{{ $abonnementActif->joursRestants() > 1 ? 's' : '' }} restant{{ $abonnementActif->joursRestants() > 1 ? 's' : '' }})
                    @endif
                </p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-green-600 font-medium">{{ $tenant->quota_factures_mensuel === -1 ? 'Illimité' : $tenant->quota_factures_mensuel . ' factures/mois' }}</p>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 flex items-center gap-3">
        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-yellow-800">Aucun abonnement actif</p>
            <p class="text-sm text-yellow-700">Choisissez un plan ci-dessous pour continuer à utiliser eCOMPTA.</p>
        </div>
    </div>
    @endif

    {{-- Grille des plans --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($plans as $plan)
        @php
            $isCurrent = $tenant->plan_slug === $plan->slug;
            $isFree    = $plan->prix_mensuel_xof === 0;
        @endphp
        <div class="bg-white rounded-xl border {{ $isCurrent ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200' }} shadow-sm flex flex-col overflow-hidden relative">

            {{-- Badge plan actuel --}}
            @if($isCurrent)
            <div class="absolute top-3 right-3">
                <span class="bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Actuel</span>
            </div>
            @endif

            {{-- En-tête plan --}}
            <div class="{{ $plan->slug === 'cabinet_plus' ? 'bg-blue-900 text-white' : 'bg-gray-50' }} px-5 py-4 border-b {{ $plan->slug === 'cabinet_plus' ? 'border-blue-800' : 'border-gray-100' }}">
                <h3 class="font-bold text-lg {{ $plan->slug === 'cabinet_plus' ? 'text-white' : 'text-gray-900' }}">
                    {{ $plan->nom }}
                </h3>
                <div class="mt-2">
                    @if($isFree)
                    <span class="text-2xl font-black {{ $plan->slug === 'cabinet_plus' ? 'text-white' : 'text-gray-900' }}">Gratuit</span>
                    @else
                    <span class="text-2xl font-black {{ $plan->slug === 'cabinet_plus' ? 'text-white' : 'text-gray-900' }}">
                        {{ number_format($plan->prix_mensuel_xof, 0, ',', ' ') }}
                    </span>
                    <span class="text-sm {{ $plan->slug === 'cabinet_plus' ? 'text-blue-200' : 'text-gray-500' }}">FCFA/mois</span>
                    @endif
                </div>
            </div>

            {{-- Caractéristiques --}}
            <div class="px-5 py-4 flex-1 space-y-2.5 text-sm">
                <div class="flex items-center gap-2 text-gray-700">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>
                        @if($plan->quota_factures_mensuel === -1)
                            Factures <strong>illimitées</strong>
                        @else
                            <strong>{{ $plan->quota_factures_mensuel }}</strong> factures/mois
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>OCR IA + SYSCOHADA auto</span>
                </div>
                <div class="flex items-center gap-2 {{ $plan->export_xlsx ? 'text-gray-700' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 {{ $plan->export_xlsx ? 'text-green-500' : 'text-gray-200' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Export Excel</span>
                </div>
                <div class="flex items-center gap-2 {{ $plan->google_sheets ? 'text-gray-700' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 {{ $plan->google_sheets ? 'text-green-500' : 'text-gray-200' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Sync Google Sheets</span>
                </div>
                <div class="flex items-center gap-2 {{ $plan->api_access ? 'text-gray-700' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 {{ $plan->api_access ? 'text-green-500' : 'text-gray-200' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Accès API REST</span>
                </div>
            </div>

            {{-- Bouton --}}
            <div class="px-5 pb-5">
                @if($isCurrent)
                <button disabled
                        class="w-full py-2.5 bg-gray-100 text-gray-400 rounded-lg text-sm font-medium cursor-not-allowed">
                    Plan actuel
                </button>
                @elseif($isFree)
                <span class="block w-full py-2.5 text-center text-gray-400 text-sm">Plan d'essai</span>
                @else
                <form method="POST" action="{{ route('abonnement.initier') }}">
                    @csrf
                    <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                    <button type="submit"
                            class="w-full py-2.5 {{ $plan->slug === 'cabinet_plus' ? 'bg-blue-900 hover:bg-blue-800' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Payer via Mobile Money
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Info paiement --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-1">Paiement sécurisé via FeexPay</p>
            <p class="text-blue-700">Accepte MTN MoMo, Moov Money et cartes bancaires. Votre abonnement est activé instantanément après confirmation du paiement.</p>
        </div>
    </div>

    {{-- Historique abonnements --}}
    @if($historiqueAbonnements->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Historique des paiements</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Plan</th>
                        <th class="px-5 py-3 text-left">Période</th>
                        <th class="px-5 py-3 text-right">Montant</th>
                        <th class="px-5 py-3 text-center">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($historiqueAbonnements as $ab)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $ab->plan_slug ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $ab->debut_le?->format('d/m/Y') ?? '—' }} → {{ $ab->expire_le?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right text-gray-800">
                            {{ $ab->montant_paye ? number_format((float)$ab->montant_paye, 0, ',', ' ') . ' FCFA' : '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $ab->statut === 'actif' ? 'bg-green-100 text-green-700' : ($ab->statut === 'expire' ? 'bg-gray-100 text-gray-500' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($ab->statut) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
