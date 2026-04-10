@extends('layouts.admin')
@section('title', 'Admin — Tableau de bord')

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord iCODE</h1>
        <p class="text-gray-500 text-sm mt-1">Vue d'ensemble de la plateforme eCOMPTA SaaS</p>
    </div>

    {{-- KPIs plateforme --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cabinets</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalTenants }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $tenantsActifs }} actifs</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Utilisateurs</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</div>
            <div class="text-xs text-gray-400 mt-1">tous cabinets</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Factures (mois)</span>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $facturesCeMois }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $facturesTotales }} au total</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">MRR</span>
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">
                {{ number_format($mrr, 0, ',', ' ') }}
            </div>
            <div class="text-xs text-gray-400 mt-1">FCFA / mois</div>
        </div>

    </div>

    {{-- Répartition par plan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="font-semibold text-gray-800 mb-4">Répartition par plan</h2>
            <div class="space-y-3">
                @foreach($repartitionPlans as $slug => $count)
                @php
                    $pct = $totalTenants > 0 ? round($count / $totalTenants * 100) : 0;
                    $colors = ['trial' => 'bg-gray-400', 'starter' => 'bg-blue-400', 'pro' => 'bg-blue-600', 'cabinet_plus' => 'bg-blue-900'];
                    $color = $colors[$slug] ?? 'bg-gray-400';
                @endphp
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $slug) }}</span>
                        <span class="text-gray-500">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $color }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Derniers cabinets inscrits --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Derniers cabinets</h2>
                <a href="{{ route('admin.tenants.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($derniersTenants as $t)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $t->nom }}</p>
                        <p class="text-xs text-gray-400">{{ $t->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $t->statut === 'actif' ? 'bg-green-100 text-green-700' : ($t->statut === 'suspendu' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($t->statut) }}
                        </span>
                        <a href="{{ route('admin.tenants.show', $t) }}" class="text-gray-400 hover:text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @empty
                <p class="px-5 py-4 text-gray-400 text-sm">Aucun cabinet.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
