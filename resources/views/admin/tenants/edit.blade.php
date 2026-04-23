@extends('layouts.admin')
@section('title', 'Modifier — '.$tenant->nom)
@section('page-title', 'Modifier le cabinet')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.tenants.show', $tenant) }}"
           class="text-gray-400 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">{{ $tenant->nom }}</h1>
        <span class="text-xs px-2 py-0.5 rounded-full font-medium
            {{ $tenant->statut==='actif'?'bg-green-100 text-green-700':($tenant->statut==='trial'?'bg-blue-100 text-blue-700':($tenant->statut==='suspendu'?'bg-red-100 text-red-700':'bg-gray-100 text-gray-500')) }}">
            {{ ucfirst($tenant->statut) }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Informations du cabinet --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Informations du cabinet</h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du cabinet *</label>
                    <input type="text" name="nom" value="{{ old('nom', $tenant->nom) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email de contact</label>
                    <input type="email" name="email_contact" value="{{ old('email_contact', $tenant->email_contact) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $tenant->telephone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IFU</label>
                    <input type="text" name="ifu" value="{{ old('ifu', $tenant->ifu) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RCCM</label>
                    <input type="text" name="rccm" value="{{ old('rccm', $tenant->rccm) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Régime fiscal *</label>
                    <select name="regime_fiscal" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="B" {{ old('regime_fiscal', $tenant->regime_fiscal) === 'B' ? 'selected' : '' }}>B — Régime du Réel Normal</option>
                        <option value="D" {{ old('regime_fiscal', $tenant->regime_fiscal) === 'D' ? 'selected' : '' }}>D — Régime Simplifié</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Plan & Abonnement --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Plan & Abonnement</h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan d'abonnement *</label>
                    <select name="plan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($plans as $plan)
                        <option value="{{ $plan->slug }}"
                            {{ old('plan', $tenant->plan) === $plan->slug ? 'selected' : '' }}>
                            {{ $plan->nom }}
                            @if($plan->prix_mensuel_xof > 0)
                                — {{ number_format($plan->prix_mensuel_xof, 0, ',', ' ') }} FCFA/mois
                            @else
                                — Gratuit ({{ $plan->duree_essai_jours ?? 15 }} jours)
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Le quota sera mis à jour selon le plan sélectionné.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="statut" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="trial"    {{ old('statut', $tenant->statut) === 'trial'    ? 'selected' : '' }}>Période d'essai</option>
                        <option value="actif"    {{ old('statut', $tenant->statut) === 'actif'    ? 'selected' : '' }}>Actif</option>
                        <option value="suspendu" {{ old('statut', $tenant->statut) === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        <option value="expire"   {{ old('statut', $tenant->statut) === 'expire'   ? 'selected' : '' }}>Expiré</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiration de l'abonnement</label>
                    <input type="date" name="abonnement_expire_le"
                           value="{{ old('abonnement_expire_le', $tenant->abonnement_expire_le?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quota factures/mois</label>
                        <input type="number" name="quota_factures_mensuel"
                               value="{{ old('quota_factures_mensuel', $tenant->quota_factures_mensuel) }}"
                               min="-1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-0.5">-1 = illimité</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quota utilisateurs</label>
                        <input type="number" name="quota_users"
                               value="{{ old('quota_users', $tenant->quota_users) }}"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Sécurité --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Sécurité — Admin du cabinet</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe admin</label>
                <input type="password" name="admin_password" placeholder="Laisser vide pour ne pas changer"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Minimum 8 caractères. Laissez vide pour conserver l'actuel.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-blue-700 text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-blue-800 transition shadow-sm">
                Enregistrer les modifications
            </button>
            <a href="{{ route('admin.tenants.show', $tenant) }}"
               class="px-4 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
                Annuler
            </a>
        </div>
    </form>

</div>
@endsection
