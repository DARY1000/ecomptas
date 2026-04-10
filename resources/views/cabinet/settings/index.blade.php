@extends('layouts.app')
@section('title', 'Paramètres du cabinet')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- En-tête --}}
    <h1 class="text-2xl font-bold text-gray-900">Paramètres du cabinet</h1>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 rounded-xl px-4 py-3">
        @foreach($errors->all() as $e)
            <p class="text-red-600 text-sm">{{ $e }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
        @csrf @method('PUT')

        {{-- Informations générales --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide">Informations du cabinet</h2>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nom du cabinet <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom', $tenant->nom) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email du cabinet</label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $tenant->telephone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Adresse</label>
                <input type="text" name="adresse" value="{{ old('adresse', $tenant->adresse) }}"
                       placeholder="Cotonou, Bénin"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">N° IFU</label>
                    <input type="text" name="ifu" value="{{ old('ifu', $tenant->ifu) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">RCCM</label>
                    <input type="text" name="rccm" value="{{ old('rccm', $tenant->rccm) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Régime fiscal</label>
                <select name="regime_fiscal"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="B" {{ old('regime_fiscal', $tenant->regime_fiscal) === 'B' ? 'selected' : '' }}>Régime B — Assujetti TVA 18%</option>
                    <option value="D" {{ old('regime_fiscal', $tenant->regime_fiscal) === 'D' ? 'selected' : '' }}>Régime D — Exonéré TVA</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Détermine le traitement SYSCOHADA des factures.</p>
            </div>
        </div>

        {{-- Intégration Google Sheets --}}
        @php $gsConfig = $tenant->config_google_sheets ?? []; @endphp
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide">Google Sheets</h2>
                @if(isset($gsConfig['spreadsheet_id']) && $gsConfig['spreadsheet_id'])
                <span class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    Configuré
                </span>
                @else
                <span class="text-xs text-gray-400">Non configuré</span>
                @endif
            </div>

            @if(!(auth()->user()->tenant->abonnementActif?->plan?->google_sheets ?? false))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 text-sm text-yellow-700 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                La synchronisation Google Sheets est disponible à partir du plan Pro.
                <a href="{{ route('abonnement.index') }}" class="underline font-medium">Voir les plans →</a>
            </div>
            @else
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ID du Spreadsheet</label>
                <input type="text" name="google_spreadsheet_id"
                       value="{{ old('google_spreadsheet_id', $gsConfig['spreadsheet_id'] ?? '') }}"
                       placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">
                    Trouvez l'ID dans l'URL de votre Google Sheet : docs.google.com/spreadsheets/d/<strong>ID</strong>/edit
                </p>
            </div>
            @endif
        </div>

        {{-- Informations plan (lecture seule) --}}
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Plan actuel</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs uppercase">Plan</p>
                    <p class="font-semibold text-gray-800 mt-0.5">{{ ucfirst(str_replace('_', ' ', $tenant->plan_slug ?? 'trial')) }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase">Quota mensuel</p>
                    <p class="font-semibold text-gray-800 mt-0.5">
                        {{ $tenant->quota_factures_mensuel === -1 ? 'Illimité' : $tenant->quota_factures_mensuel . ' factures' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase">Expiration</p>
                    <p class="font-semibold {{ $tenant->abonnement_expire_le && $tenant->abonnement_expire_le->isPast() ? 'text-red-600' : 'text-gray-800' }} mt-0.5">
                        {{ $tenant->abonnement_expire_le?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('abonnement.index') }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline">
                Gérer l'abonnement →
            </a>
        </div>

        {{-- Bouton enregistrer --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>

</div>
@endsection
