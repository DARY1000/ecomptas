@extends('layouts.app')
@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Ajouter un utilisateur</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 rounded-xl px-4 py-3">
        @foreach($errors->all() as $e)
            <p class="text-red-600 text-sm">{{ $e }}</p>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Prénom Nom"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Adresse email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="utilisateur@cabinet.bj"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Rôle <span class="text-red-500">*</span></label>
                <select name="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Sélectionner un rôle —</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin — Accès complet au cabinet</option>
                    <option value="comptable" {{ old('role') === 'comptable' ? 'selected' : '' }}>Comptable — Upload + validation factures</option>
                    <option value="auditeur" {{ old('role') === 'auditeur' ? 'selected' : '' }}>Auditeur — Lecture seule</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Mot de passe temporaire <span class="text-red-500">*</span></label>
                <input type="text" name="password" value="{{ old('password', Str::random(10)) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">L'utilisateur devra changer son mot de passe à la première connexion.</p>
            </div>

            {{-- Résumé des rôles --}}
            <div class="bg-gray-50 rounded-lg p-4 text-xs text-gray-500 space-y-1.5 mt-2">
                <p class="font-semibold text-gray-700 mb-2">Permissions par rôle :</p>
                <p><strong class="text-blue-700">Admin</strong> : Gestion complète (factures, écritures, utilisateurs, paramètres)</p>
                <p><strong class="text-purple-700">Comptable</strong> : Upload, traitement et validation de factures</p>
                <p><strong class="text-green-700">Auditeur</strong> : Consultation uniquement (lecture seule)</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-900 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-800 transition text-sm">
                    Créer l'utilisateur
                </button>
                <a href="{{ route('users.index') }}"
                   class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                    Annuler
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
