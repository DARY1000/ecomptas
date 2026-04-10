@extends('layouts.admin')
@section('title', 'Nouveau cabinet')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.tenants.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Nouveau cabinet</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 rounded-xl px-4 py-3">
        @foreach($errors->all() as $e)
            <p class="text-red-600 text-sm">{{ $e }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.tenants.store') }}" class="space-y-5">
        @csrf

        {{-- Informations cabinet --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide">Informations du cabinet</h2>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nom du cabinet <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom') }}" required
                       placeholder="Cabinet Dupont & Associés"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email cabinet</label>
                    <input type="email" name="email_cabinet" value="{{ old('email_cabinet') }}"
                           placeholder="contact@cabinet.bj"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}"
                           placeholder="+229 97 00 00 00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">N° IFU (Identifiant Fiscal Unique)</label>
                <input type="text" name="ifu" value="{{ old('ifu') }}"
                       placeholder="3201901B0001"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">RCCM</label>
                    <input type="text" name="rccm" value="{{ old('rccm') }}"
                           placeholder="RB/COT/19 A 12345"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Régime fiscal</label>
                    <select name="regime_fiscal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="B" {{ old('regime_fiscal', 'B') === 'B' ? 'selected' : '' }}>Régime B — TVA</option>
                        <option value="D" {{ old('regime_fiscal') === 'D' ? 'selected' : '' }}>Régime D — Exonéré TVA</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Plan & abonnement --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide">Plan & abonnement</h2>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Plan <span class="text-red-500">*</span></label>
                    <select name="plan_slug" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($plans as $plan)
                        <option value="{{ $plan->slug }}" {{ old('plan_slug', 'trial') === $plan->slug ? 'selected' : '' }}>
                            {{ $plan->nom }} — {{ $plan->prix_mensuel_xof === 0 ? 'Gratuit' : number_format($plan->prix_mensuel_xof, 0, ',', ' ') . ' FCFA/mois' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Statut</label>
                    <select name="statut"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="trial" {{ old('statut', 'trial') === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="actif" {{ old('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="suspendu" {{ old('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date d'expiration abonnement</label>
                <input type="date" name="abonnement_expire_le"
                       value="{{ old('abonnement_expire_le', now()->addDays(30)->format('Y-m-d')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Compte admin --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide">Compte administrateur</h2>
            <p class="text-xs text-gray-500">Un compte admin sera créé pour accéder à ce cabinet.</p>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                       placeholder="Jean Dupont"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email administrateur <span class="text-red-500">*</span></label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                       placeholder="admin@cabinet.bj"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Mot de passe temporaire <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" name="admin_password" value="{{ old('admin_password', Str::random(12)) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>
                <p class="text-xs text-gray-400 mt-1">Communiquer ce mot de passe à l'administrateur du cabinet.</p>
            </div>
        </div>

        {{-- Boutons --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.tenants.index') }}"
               class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                Annuler
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition text-sm font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Créer le cabinet
            </button>
        </div>

    </form>
</div>
@endsection
