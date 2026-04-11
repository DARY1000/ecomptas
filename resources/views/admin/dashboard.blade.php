@extends('layouts.admin')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">

    {{-- ── KPIs principaux ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Cabinets actifs</span>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ $tenantsActifs }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $totalTenants }} total · {{ $tenantsTrial }} en essai</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">MRR</span>
                <div class="w-9 h-9 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ number_format($mrr, 0, ',', ' ') }}</div>
            <div class="text-xs text-gray-400 mt-1">FCFA/mois · {{ number_format($revenuTotal, 0, ',', ' ') }} FCFA total</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Factures ce mois</span>
                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ $facturesCeMois }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $facturesTotales }} total · {{ $facturesErreur }} erreurs</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Utilisateurs</span>
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ $totalUsers }}</div>
            <div class="text-xs text-gray-400 mt-1">sur {{ $totalTenants }} cabinets</div>
        </div>
    </div>

    {{-- ── Ligne 2 ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="font-semibold text-gray-800 mb-4">Répartition par plan</h2>
            <div class="space-y-3">
                @foreach($repartitionPlans as $slug => $count)
                @php
                    $pct  = $totalTenants > 0 ? round($count/$totalTenants*100) : 0;
                    $bars = ['trial'=>'bg-gray-400','starter'=>'bg-blue-400','pro'=>'bg-blue-600','cabinet'=>'bg-teal-500'];
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700 capitalize">{{ ucfirst($slug) }}</span>
                        <span class="text-gray-400">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $bars[$slug] ?? 'bg-gray-300' }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-5 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.plans.index') }}" class="text-sm text-blue-600 hover:underline font-medium">Gérer les plans →</a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Paiements récents</h2>
                <a href="{{ route('admin.abonnements.index') }}" class="text-xs text-blue-600 hover:underline">Voir tout →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($derniersAbonnements as $ab)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $ab->tenant?->nom ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $ab->created_at->format('d/m/Y') }} · {{ $ab->plan?->nom }}</p>
                    </div>
                    <span class="text-sm font-bold text-green-700">+{{ number_format($ab->montant_xof, 0, ',', ' ') }}</span>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">Aucun paiement.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Nouveaux cabinets</h2>
                <a href="{{ route('admin.tenants.index') }}" class="text-xs text-blue-600 hover:underline">Voir tout →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($derniersTenants as $t)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $t->nom }}</p>
                        <p class="text-xs text-gray-400">{{ $t->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $t->statut==='actif'?'bg-green-100 text-green-700':($t->statut==='trial'?'bg-blue-100 text-blue-700':($t->statut==='suspendu'?'bg-red-100 text-red-700':'bg-gray-100 text-gray-500')) }}">
                        {{ ucfirst($t->statut) }}
                    </span>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">Aucun cabinet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Ligne 3 : Monitoring + Essais expirant ───────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Monitoring IA — ce mois</h2>
                <a href="{{ route('admin.monitoring') }}" class="text-xs text-blue-600 hover:underline">Détails →</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <div class="text-3xl font-black text-green-700">{{ $facturesValidees }}</div>
                    <div class="text-xs text-green-600 mt-1 font-medium">Validées</div>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4 text-center">
                    <div class="text-3xl font-black text-yellow-700">{{ $facturesEnCours }}</div>
                    <div class="text-xs text-yellow-600 mt-1 font-medium">En traitement</div>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <div class="text-3xl font-black text-blue-700">{{ $facturesAValider }}</div>
                    <div class="text-xs text-blue-600 mt-1 font-medium">À valider</div>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <div class="text-3xl font-black text-red-700">{{ $facturesErreur }}</div>
                    <div class="text-xs text-red-600 mt-1 font-medium">Erreurs</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Essais expirant dans 7 jours</h2>
                <span class="text-xs bg-orange-100 text-orange-700 px-2.5 py-0.5 rounded-full font-bold">{{ $trialsExpirant->count() }}</span>
            </div>
            @if($trialsExpirant->isEmpty())
                <p class="px-5 py-8 text-gray-400 text-sm text-center">Aucun essai n'expire prochainement. ✅</p>
            @else
                <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                    @foreach($trialsExpirant as $t)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $t->nom }}</p>
                            <p class="text-xs text-gray-400">{{ $t->email_contact }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-orange-600">{{ $t->abonnement_expire_le->diffForHumans() }}</p>
                            <a href="{{ route('admin.tenants.show', $t) }}" class="text-xs text-blue-500 hover:underline">Voir →</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
