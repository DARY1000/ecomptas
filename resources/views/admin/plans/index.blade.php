@extends('layouts.admin')
@section('title', 'Gestion des Plans')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Plans d'abonnement</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $plans->count() }} plans configurés</p>
        </div>
        <a href="{{ route('admin.plans.create') }}"
           class="bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-blue-800 transition shadow">
            + Nouveau plan
        </a>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-5">
        @foreach($plans as $plan)
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden
                    {{ !$plan->actif ? 'opacity-50' : '' }}">
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-bold text-lg text-gray-900">{{ $plan->nom }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                 {{ $plan->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $plan->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <div class="text-2xl font-black text-blue-700 mb-1">{{ $plan->prix_formatté }}</div>
                <div class="text-xs text-gray-400 mb-4">slug: {{ $plan->slug }}</div>

                <ul class="space-y-1.5 text-sm text-gray-600 mb-5">
                    <li>📄 {{ $plan->quota_factures >= 9999 ? 'Illimité' : $plan->quota_factures }} factures/mois</li>
                    <li>👥 {{ $plan->quota_users >= 99 ? 'Illimité' : $plan->quota_users }} utilisateur(s)</li>
                    <li class="{{ $plan->export_xlsx ? 'text-green-600' : 'text-gray-300' }}">
                        {{ $plan->export_xlsx ? '✓' : '✗' }} Export Excel/CSV
                    </li>
                    <li class="{{ $plan->google_sheets ? 'text-green-600' : 'text-gray-300' }}">
                        {{ $plan->google_sheets ? '✓' : '✗' }} Google Sheets
                    </li>
                    <li class="{{ $plan->api_access ? 'text-green-600' : 'text-gray-300' }}">
                        {{ $plan->api_access ? '✓' : '✗' }} Accès API
                    </li>
                </ul>

                <div class="flex gap-2">
                    <a href="{{ route('admin.plans.edit', $plan) }}"
                       class="flex-1 text-center bg-blue-50 text-blue-700 font-semibold py-2 rounded-lg text-sm hover:bg-blue-100 transition">
                        Modifier
                    </a>
                    @if($plan->slug !== 'trial')
                    <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                          onsubmit="return confirm('Désactiver ce plan ?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="bg-red-50 text-red-600 font-semibold py-2 px-3 rounded-lg text-sm hover:bg-red-100 transition">
                            ✕
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
