@extends('layouts.app')
@section('title', 'Modifier — ' . $user->name)

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Modifier l'utilisateur</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 rounded-xl px-4 py-3">
        @foreach($errors->all() as $e)
            <p class="text-red-600 text-sm">{{ $e }}</p>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Adresse email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Rôle <span class="text-red-500">*</span></label>
                <select name="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="comptable" {{ old('role', $user->role) === 'comptable' ? 'selected' : '' }}>Comptable</option>
                    <option value="auditeur" {{ old('role', $user->role) === 'auditeur' ? 'selected' : '' }}>Auditeur</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nouveau mot de passe <span class="text-gray-400 font-normal">(laisser vide pour ne pas changer)</span></label>
                <input type="password" name="password"
                       placeholder="••••••••"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-900 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-800 transition text-sm">
                    Enregistrer
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
