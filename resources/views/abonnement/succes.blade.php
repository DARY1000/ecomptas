@extends('layouts.app')
@section('title', 'Paiement confirmé')

@section('content')
<div class="max-w-lg mx-auto py-12 text-center space-y-6">

    {{-- Icône succès --}}
    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mx-auto">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    {{-- Message principal --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Paiement confirmé !</h1>
        <p class="text-gray-500">
            Votre abonnement est maintenant actif. Vous pouvez commencer à uploader vos factures.
        </p>
    </div>

    {{-- Détails abonnement --}}
    @if(isset($abonnement))
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-left space-y-3">
        <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide">Détails de l'abonnement</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500">Plan</dt>
                <dd class="font-semibold text-gray-800">{{ ucfirst($abonnement->plan_slug ?? '—') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Factures / mois</dt>
                <dd class="font-medium text-gray-800">
                    @php $tenant = auth()->user()->tenant; @endphp
                    {{ $tenant->quota_factures_mensuel === -1 ? 'Illimité' : $tenant->quota_factures_mensuel }}
                </dd>
            </div>
            @if($abonnement->expire_le)
            <div class="flex justify-between">
                <dt class="text-gray-500">Valable jusqu'au</dt>
                <dd class="font-medium text-gray-800">{{ $abonnement->expire_le->format('d/m/Y') }}</dd>
            </div>
            @endif
            @if($abonnement->montant_paye)
            <div class="flex justify-between border-t border-gray-100 pt-2 mt-2">
                <dt class="text-gray-500 font-medium">Montant payé</dt>
                <dd class="font-bold text-gray-900">{{ number_format((float)$abonnement->montant_paye, 0, ',', ' ') }} FCFA</dd>
            </div>
            @endif
        </dl>
    </div>
    @endif

    {{-- CTA --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center gap-2 bg-blue-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Tableau de bord
        </a>
        <a href="{{ route('factures.upload') }}"
           class="inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Uploader une facture
        </a>
    </div>

</div>
@endsection
