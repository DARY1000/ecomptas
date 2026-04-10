@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('content')
<div class="space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
        <a href="{{ route('users.create') }}"
           class="inline-flex items-center gap-2 bg-blue-900 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition font-medium text-sm shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un utilisateur
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($users->isEmpty())
        <div class="px-6 py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="font-medium">Aucun utilisateur pour ce cabinet</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Utilisateur</th>
                    <th class="px-5 py-3 text-left">Rôle</th>
                    <th class="px-5 py-3 text-left">Dernière connexion</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $u)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-700 font-semibold text-xs">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $u->name }}</p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @php
                            $roleColors = [
                                'admin' => 'bg-blue-100 text-blue-700',
                                'comptable' => 'bg-purple-100 text-purple-700',
                                'auditeur' => 'bg-green-100 text-green-700',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $roleColors[$u->role] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $u->role_label }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">
                        {{ $u->last_login_at?->diffForHumans() ?? 'Jamais connecté' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($u->deleted_at)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            Inactif
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Actif
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($u->id !== auth()->id())
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('users.edit', $u) }}"
                               class="text-gray-400 hover:text-blue-600 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if(!$u->deleted_at)
                            <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline"
                                  onsubmit="return confirm('Désactiver cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Désactiver">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @else
                        <span class="text-xs text-gray-400">Vous</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Quota utilisateurs --}}
    @php $tenant = auth()->user()->tenant; @endphp
    @if($tenant->quota_users)
    <p class="text-xs text-gray-400 text-center">
        {{ $users->count() }} / {{ $tenant->quota_users }} utilisateurs autorisés sur votre plan
    </p>
    @endif

</div>
@endsection
