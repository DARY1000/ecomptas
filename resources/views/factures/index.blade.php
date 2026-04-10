@extends('layouts.app')
@section('title', 'Factures')

@section('content')
<div class="space-y-5">

    {{-- En-tête + bouton upload --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Factures</h1>
        @if(!auth()->user()->estAuditeur())
        <a href="{{ route('factures.upload') }}"
           class="inline-flex items-center gap-2 bg-blue-900 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition font-medium text-sm shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Uploader des factures
        </a>
        @endif
    </div>

    {{-- ── Filtres ───────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('factures.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Rechercher (N°, fournisseur…)"
                   class="col-span-2 md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les types</option>
                <option value="ACHAT" {{ request('type') === 'ACHAT' ? 'selected' : '' }}>Achat</option>
                <option value="VENTE" {{ request('type') === 'VENTE' ? 'selected' : '' }}>Vente</option>
                <option value="CHARGE" {{ request('type') === 'CHARGE' ? 'selected' : '' }}>Charge</option>
            </select>
            <select name="statut" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="uploade" {{ request('statut') === 'uploade' ? 'selected' : '' }}>Uploadé</option>
                <option value="traitement_en_cours" {{ request('statut') === 'traitement_en_cours' ? 'selected' : '' }}>En traitement</option>
                <option value="a_valider" {{ request('statut') === 'a_valider' ? 'selected' : '' }}>À valider</option>
                <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Validé</option>
                <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                <option value="erreur" {{ request('statut') === 'erreur' ? 'selected' : '' }}>Erreur</option>
            </select>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    Filtrer
                </button>
                @if(request()->hasAny(['q', 'type', 'statut', 'date_debut', 'date_fin']))
                <a href="{{ route('factures.index') }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-500 hover:bg-gray-50 transition">
                    ✕
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- ── Tableau des factures ──────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($factures->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-medium">Aucune facture trouvée</p>
                @if(!auth()->user()->estAuditeur())
                <a href="{{ route('factures.upload') }}" class="text-blue-600 hover:underline text-sm mt-2 block">
                    Uploader votre première facture →
                </a>
                @endif
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Document</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-left">Date facture</th>
                        <th class="px-5 py-3 text-right">Montant TTC</th>
                        <th class="px-5 py-3 text-center">Statut</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($factures as $f)
                    @php
                        $badgeClass = match($f->statut) {
                            'valide'              => 'bg-green-100 text-green-700',
                            'a_valider'           => 'bg-yellow-100 text-yellow-700',
                            'traitement_en_cours' => 'bg-blue-100 text-blue-700',
                            'rejete','erreur'     => 'bg-red-100 text-red-700',
                            default               => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition" id="row-{{ $f->id }}">
                        <td class="px-5 py-3">
                            <a href="{{ route('factures.show', $f) }}"
                               class="font-medium text-blue-700 hover:underline block">
                                {{ $f->numero_facture ?? substr($f->pdf_nom_original, 0, 25) . '…' }}
                            </a>
                            <span class="text-xs text-gray-400">{{ $f->fournisseur_client }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if($f->type_document)
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $f->type_document === 'VENTE' ? 'bg-green-100 text-green-700' : ($f->type_document === 'ACHAT' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                {{ $f->type_document }}
                            </span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $f->date_facture?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800">
                            {{ $f->montant_ttc ? number_format((float)$f->montant_ttc, 0, ',', ' ') . ' FCFA' : '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}"
                                  @if($f->statut === 'traitement_en_cours') id="badge-{{ $f->id }}" @endif>
                                {{ $f->statut_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('factures.show', $f) }}"
                                   class="text-gray-400 hover:text-blue-600 transition" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($f->statut === 'a_valider' && auth()->user()->peutValider())
                                <a href="{{ route('factures.revue', $f) }}"
                                   class="text-yellow-500 hover:text-yellow-700 transition text-xs font-medium" title="Réviser">
                                    ✏️
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @if($f->statut === 'traitement_en_cours')
                    @push('scripts')
                    <script>pollStatutFacture('{{ $f->id }}');</script>
                    @endpush
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $factures->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
