@extends('layouts.admin')
@section('title', 'Cabinet — ' . $tenant->nom)

@section('content')
<div class="space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tenants.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $tenant->nom }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $tenant->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php
                $statutColors = [
                    'actif' => 'bg-green-100 text-green-700 border-green-200',
                    'trial' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'suspendu' => 'bg-red-100 text-red-700 border-red-200',
                    'expire' => 'bg-gray-100 text-gray-600 border-gray-200',
                ];
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $statutColors[$tenant->statut] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                {{ ucfirst($tenant->statut) }}
            </span>
            @if($tenant->statut === 'suspendu')
            <form method="POST" action="{{ route('admin.tenants.activer', $tenant) }}" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-1.5 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                    Activer
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.tenants.suspendre', $tenant) }}" class="inline"
                  onsubmit="return confirm('Suspendre ce cabinet ? Les utilisateurs ne pourront plus se connecter.')">
                @csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-1.5 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                    Suspendre
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Infos cabinet --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4 text-sm uppercase tracking-wide">Informations</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">IFU</dt>
                        <dd class="font-mono text-gray-800 mt-0.5">{{ $tenant->ifu ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">RCCM</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $tenant->rccm ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">Téléphone</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $tenant->telephone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">Régime fiscal</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $tenant->regime_fiscal === 'D' ? 'Régime D (exonéré TVA)' : 'Régime B (TVA 18%)' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">Inscrit le</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $tenant->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4 text-sm uppercase tracking-wide">Abonnement</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">Plan actuel</dt>
                        <dd class="font-semibold text-gray-900 mt-0.5">{{ ucfirst(str_replace('_', ' ', $tenant->plan ?? 'trial')) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">Quota mensuel</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $tenant->quota_factures_mensuel === -1 ? 'Illimité' : $tenant->quota_factures_mensuel . ' factures' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">Expiration</dt>
                        <dd class="{{ $tenant->abonnement_expire_le && $tenant->abonnement_expire_le->isPast() ? 'text-red-600 font-semibold' : 'text-gray-700' }} mt-0.5">
                            {{ $tenant->abonnement_expire_le?->format('d/m/Y') ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase">Factures ce mois</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $tenant->facturesCeMois() }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Utilisateurs + factures récentes --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Statistiques --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_factures'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Factures total</div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['factures_valides'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Validées</div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Utilisateurs</div>
                </div>
            </div>

            {{-- Utilisateurs --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Utilisateurs</h2>
                </div>
                @if($tenant->users->isEmpty())
                <p class="px-5 py-4 text-gray-400 text-sm">Aucun utilisateur.</p>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($tenant->users as $u)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $u->name }}</p>
                            <p class="text-xs text-gray-400">{{ $u->email }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-600">
                                {{ $u->role_label }}
                            </span>
                            @if($u->last_login_at)
                            <span class="text-xs text-gray-400" title="Dernière connexion">
                                {{ $u->last_login_at->diffForHumans() }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Dernières factures --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Dernières factures</h2>
                </div>
                @if($dernieresFactures->isEmpty())
                <p class="px-5 py-4 text-gray-400 text-sm">Aucune facture.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-2 text-left">Document</th>
                                <th class="px-5 py-2 text-right">Montant TTC</th>
                                <th class="px-5 py-2 text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($dernieresFactures as $f)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-2">
                                    <p class="font-medium text-gray-800">{{ $f->numero_facture ?? $f->pdf_nom_original }}</p>
                                    <p class="text-xs text-gray-400">{{ $f->created_at->format('d/m/Y') }}</p>
                                </td>
                                <td class="px-5 py-2 text-right text-gray-700">
                                    {{ $f->montant_ttc ? number_format((float)$f->montant_ttc, 0, ',', ' ') . ' FCFA' : '—' }}
                                </td>
                                <td class="px-5 py-2 text-center">
                                    @php
                                        $colors = [
                                            'valide' => 'bg-green-100 text-green-700',
                                            'a_valider' => 'bg-yellow-100 text-yellow-700',
                                            'traitement_en_cours' => 'bg-blue-100 text-blue-700',
                                            'rejete' => 'bg-red-100 text-red-700',
                                            'erreur' => 'bg-red-100 text-red-700',
                                            'uploade' => 'bg-gray-100 text-gray-600',
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

    </div>

</div>
@endsection
