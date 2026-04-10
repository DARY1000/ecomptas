@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">

    {{-- ── En-tête ──────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ $tenant->nom }} — {{ now()->translatedFormat('F Y') }}
            </p>
        </div>
        @if(!auth()->user()->estAuditeur())
        <a href="{{ route('factures.upload') }}"
           class="inline-flex items-center gap-2 bg-blue-900 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition font-medium text-sm shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Uploader une facture
        </a>
        @endif
    </div>

    {{-- ── KPIs ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Factures ce mois --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ce mois</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $facturesCeMois }}</div>
            <div class="text-xs text-gray-400 mt-1">/ {{ $tenant->quota_factures_mensuel }} autorisées</div>
            {{-- Barre de progression quota --}}
            @php $pct = $tenant->quota_factures_mensuel > 0 ? min(100, round($facturesCeMois / $tenant->quota_factures_mensuel * 100)) : 0; @endphp
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $pct >= 80 ? 'bg-yellow-500' : 'bg-blue-500' }}"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- À valider --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">À valider</span>
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold {{ $facturesAValider > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                {{ $facturesAValider }}
            </div>
            @if($facturesAValider > 0)
            <a href="{{ route('factures.index', ['statut' => 'a_valider']) }}"
               class="text-xs text-yellow-600 hover:underline mt-1 block">Voir les factures →</a>
            @else
            <div class="text-xs text-gray-400 mt-1">Aucune en attente</div>
            @endif
        </div>

        {{-- En traitement IA --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">IA en cours</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-purple-600">{{ $facturesEnCours }}</div>
            <div class="text-xs text-gray-400 mt-1">Pipeline n8n.cloud</div>
        </div>

        {{-- Erreurs --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Erreurs</span>
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold {{ $facturesErreur > 0 ? 'text-red-600' : 'text-gray-900' }}">
                {{ $facturesErreur }}
            </div>
            <div class="text-xs text-gray-400 mt-1">
                {{ $facturesErreur > 0 ? 'À corriger' : 'Aucune erreur' }}
            </div>
        </div>

    </div>

    {{-- ── Montants du mois ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">Total Achats validés</h3>
            <div class="text-2xl font-bold text-gray-900">
                {{ number_format((float) $totalAchats, 0, ',', ' ') }} <span class="text-sm text-gray-400">FCFA</span>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">Total Ventes validées</h3>
            <div class="text-2xl font-bold text-gray-900">
                {{ number_format((float) $totalVentes, 0, ',', ' ') }} <span class="text-sm text-gray-400">FCFA</span>
            </div>
        </div>
    </div>

    {{-- ── Dernières factures ───────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Dernières factures</h2>
            <a href="{{ route('factures.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout →</a>
        </div>
        @if($dernieresFactures->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-lg mb-2">Aucune facture pour le moment</p>
                @if(!auth()->user()->estAuditeur())
                <a href="{{ route('factures.upload') }}" class="text-blue-600 hover:underline text-sm">
                    Uploader votre première facture →
                </a>
                @endif
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Facture</th>
                        <th class="px-6 py-3 text-left">Type</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-right">Montant TTC</th>
                        <th class="px-6 py-3 text-center">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($dernieresFactures as $f)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <a href="{{ route('factures.show', $f) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $f->numero_facture ?? $f->pdf_nom_original }}
                            </a>
                            <div class="text-xs text-gray-400">{{ $f->fournisseur_client }}</div>
                        </td>
                        <td class="px-6 py-3">
                            @if($f->type_document)
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    {{ $f->type_document === 'VENTE' ? 'bg-green-100 text-green-700' : ($f->type_document === 'ACHAT' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $f->type_document }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $f->date_facture?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-3 text-right font-medium">
                            {{ $f->montant_formatté ?? '—' }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            @php
                                $colors = [
                                    'uploade' => 'bg-gray-100 text-gray-600',
                                    'traitement_en_cours' => 'bg-blue-100 text-blue-700',
                                    'a_valider' => 'bg-yellow-100 text-yellow-700',
                                    'valide' => 'bg-green-100 text-green-700',
                                    'rejete' => 'bg-red-100 text-red-700',
                                    'erreur' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$f->statut] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $f->statut_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
