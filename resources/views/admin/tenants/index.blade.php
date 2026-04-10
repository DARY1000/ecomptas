@extends('layouts.admin')
@section('title', 'Admin — Cabinets')

@section('content')
<div class="space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Cabinets clients</h1>
        <a href="{{ route('admin.tenants.create') }}"
           class="inline-flex items-center gap-2 bg-blue-900 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition font-medium text-sm shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau cabinet
        </a>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ route('admin.tenants.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Nom, email, RCCM…"
                   class="col-span-2 md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="statut" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="trial" {{ request('statut') === 'trial' ? 'selected' : '' }}>Trial</option>
                <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                <option value="expire" {{ request('statut') === 'expire' ? 'selected' : '' }}>Expiré</option>
            </select>
            <select name="plan" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les plans</option>
                <option value="trial" {{ request('plan') === 'trial' ? 'selected' : '' }}>Trial</option>
                <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter</option>
                <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                <option value="cabinet_plus" {{ request('plan') === 'cabinet_plus' ? 'selected' : '' }}>Cabinet+</option>
            </select>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    Filtrer
                </button>
                @if(request()->hasAny(['q', 'statut', 'plan']))
                <a href="{{ route('admin.tenants.index') }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-500 hover:bg-gray-50 transition">
                    ✕
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($tenants->isEmpty())
        <div class="px-6 py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
            </svg>
            <p class="font-medium">Aucun cabinet trouvé</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Cabinet</th>
                        <th class="px-5 py-3 text-left">Plan</th>
                        <th class="px-5 py-3 text-center">Quota</th>
                        <th class="px-5 py-3 text-left">Expiration</th>
                        <th class="px-5 py-3 text-center">Statut</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tenants as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.tenants.show', $t) }}"
                               class="font-medium text-blue-700 hover:underline block">
                                {{ $t->nom }}
                            </a>
                            <span class="text-xs text-gray-400">{{ $t->email }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $planColors = [
                                    'trial' => 'bg-gray-100 text-gray-600',
                                    'starter' => 'bg-blue-100 text-blue-700',
                                    'pro' => 'bg-purple-100 text-purple-700',
                                    'cabinet_plus' => 'bg-blue-900 text-white',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $planColors[$t->plan] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $t->plan ?? 'trial')) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center text-gray-600">
                            {{ $t->facturesCeMois() }} / {{ $t->quota_factures_mensuel === -1 ? '∞' : $t->quota_factures_mensuel }}
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $t->abonnement_expire_le?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $statutColors = [
                                    'actif' => 'bg-green-100 text-green-700',
                                    'trial' => 'bg-blue-100 text-blue-700',
                                    'suspendu' => 'bg-red-100 text-red-700',
                                    'expire' => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statutColors[$t->statut] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($t->statut) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.tenants.show', $t) }}"
                                   class="text-gray-400 hover:text-blue-600 transition" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($t->statut === 'suspendu')
                                <form method="POST" action="{{ route('admin.tenants.activer', $t) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-green-500 hover:text-green-700 transition text-xs font-medium" title="Activer">
                                        ✓
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.tenants.suspendre', $t) }}" class="inline"
                                      onsubmit="return confirm('Suspendre ce cabinet ?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition text-xs font-medium" title="Suspendre">
                                        ✕
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
