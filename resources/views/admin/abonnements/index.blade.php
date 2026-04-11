@extends('layouts.admin')
@section('title', 'Abonnements & Paiements')
@section('page-title', 'Abonnements & Paiements')

@section('content')
<div class="space-y-6">

    {{-- ── Métriques revenue ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Revenu ce mois</p>
            <p class="text-2xl font-black text-green-700">{{ number_format($revenuCeMois, 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">FCFA</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Revenu total</p>
            <p class="text-2xl font-black text-gray-900">{{ number_format($revenuTotal, 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">FCFA cumulé</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Abonnements actifs</p>
            <p class="text-2xl font-black text-blue-700">{{ $totalAbonnements }}</p>
            <p class="text-xs text-gray-400 mt-1">cabinets payants</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">En attente</p>
            <p class="text-2xl font-black {{ $enAttente > 0 ? 'text-orange-600' : 'text-gray-400' }}">{{ $enAttente }}</p>
            <p class="text-xs text-gray-400 mt-1">paiements non confirmés</p>
        </div>
    </div>

    {{-- ── Revenu par plan ──────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Revenu par plan (abonnements actifs)</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($revenuParPlan as $item)
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-sm font-bold text-gray-700">{{ $item['nom'] }}</p>
                <p class="text-xl font-black text-blue-700 mt-1">{{ number_format($item['total'], 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $item['count'] }} cabinet(s) · FCFA</p>
            </div>
            @empty
            <p class="col-span-4 text-gray-400 text-sm text-center py-4">Aucun abonnement actif.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Filtres ──────────────────────────────────────────────── --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <select name="statut" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">Tous les statuts</option>
                <option value="actif"      {{ request('statut')==='actif'       ? 'selected':'' }}>Actif</option>
                <option value="en_attente" {{ request('statut')==='en_attente'  ? 'selected':'' }}>En attente</option>
                <option value="expire"     {{ request('statut')==='expire'      ? 'selected':'' }}>Expiré</option>
                <option value="annule"     {{ request('statut')==='annule'      ? 'selected':'' }}>Annulé</option>
            </select>
            <select name="plan_id" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">Tous les plans</option>
                @foreach($plans as $p)
                <option value="{{ $p->id }}" {{ request('plan_id')==$p->id ? 'selected':'' }}>{{ $p->nom }}</option>
                @endforeach
            </select>
            <input type="month" name="mois" value="{{ request('mois') }}"
                   class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Filtrer</button>
                @if(request()->hasAny(['statut','plan_id','mois']))
                <a href="{{ route('admin.abonnements.index') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-500">✕</a>
                @endif
            </div>
        </div>
    </form>

    {{-- ── Tableau abonnements ──────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($abonnements->isEmpty())
            <p class="px-6 py-12 text-center text-gray-400">Aucun abonnement trouvé.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Cabinet</th>
                        <th class="px-5 py-3 text-left">Plan</th>
                        <th class="px-5 py-3 text-right">Montant</th>
                        <th class="px-5 py-3 text-center">Paiement</th>
                        <th class="px-5 py-3 text-center">Statut</th>
                        <th class="px-5 py-3 text-left">Période</th>
                        <th class="px-5 py-3 text-left">Transaction</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($abonnements as $ab)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.tenants.show', $ab->tenant_id) }}"
                               class="font-medium text-blue-700 hover:underline">
                                {{ $ab->tenant?->nom ?? '—' }}
                            </a>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $ab->plan?->nom ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-gray-900">
                            {{ number_format($ab->montant_xof, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3 text-center text-xs text-gray-500 uppercase">
                            {{ $ab->processeur_paiement ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $sc = ['actif'=>'bg-green-100 text-green-700','en_attente'=>'bg-yellow-100 text-yellow-700','expire'=>'bg-gray-100 text-gray-500','annule'=>'bg-red-100 text-red-600'];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc[$ab->statut] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst(str_replace('_', ' ', $ab->statut)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $ab->debut_le?->format('d/m/Y') }} → {{ $ab->expire_le?->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-400 font-mono truncate max-w-[120px]">
                            {{ $ab->transaction_id ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $abonnements->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
