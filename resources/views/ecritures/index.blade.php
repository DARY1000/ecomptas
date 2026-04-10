@extends('layouts.app')
@section('title', 'Journal comptable')

@section('content')
<div class="space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Journal comptable</h1>
        <div class="flex gap-2">
            <a href="{{ route('export.csv', request()->query()) }}"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
            @if(auth()->user()->tenant->plan && auth()->user()->tenant->plan->export_xlsx ?? false)
            <a href="{{ route('export.xlsx', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            @endif
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ route('ecritures.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Compte, libellé…"
                   class="col-span-2 md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les types</option>
                <option value="ACHAT" {{ request('type') === 'ACHAT' ? 'selected' : '' }}>Achat</option>
                <option value="VENTE" {{ request('type') === 'VENTE' ? 'selected' : '' }}>Vente</option>
                <option value="CHARGE" {{ request('type') === 'CHARGE' ? 'selected' : '' }}>Charge</option>
            </select>

            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   title="Date début">

            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   title="Date fin">

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    Filtrer
                </button>
                @if(request()->hasAny(['q', 'type', 'date_debut', 'date_fin']))
                <a href="{{ route('ecritures.index') }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-500 hover:bg-gray-50 transition">
                    ✕
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Totaux --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Débit</p>
            <p class="text-xl font-bold text-gray-900">
                {{ number_format((float)$totalDebit, 0, ',', ' ') }}
                <span class="text-sm text-gray-400 font-normal">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Crédit</p>
            <p class="text-xl font-bold text-gray-900">
                {{ number_format((float)$totalCredit, 0, ',', ' ') }}
                <span class="text-sm text-gray-400 font-normal">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 col-span-2 md:col-span-1">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Solde</p>
            @php $solde = $totalDebit - $totalCredit; @endphp
            <p class="text-xl font-bold {{ $solde >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                {{ $solde >= 0 ? '' : '-' }}{{ number_format(abs($solde), 0, ',', ' ') }}
                <span class="text-sm text-gray-400 font-normal">FCFA</span>
            </p>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($ecritures->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-medium">Aucune écriture trouvée</p>
                <p class="text-sm mt-1">Les écritures apparaissent après validation des factures.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">N° Compte</th>
                        <th class="px-5 py-3 text-left">Libellé</th>
                        <th class="px-5 py-3 text-left">Facture</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-right">Débit</th>
                        <th class="px-5 py-3 text-right">Crédit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ecritures as $e)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">
                            {{ $e->date_ecriture?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">
                                {{ $e->numero_compte }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-700 max-w-xs">
                            <span class="block truncate" title="{{ $e->libelle }}">{{ $e->libelle }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if($e->facture)
                            <a href="{{ route('factures.show', $e->facture) }}"
                               class="text-blue-600 hover:underline text-xs">
                                {{ $e->facture->numero_facture ?? Str::limit($e->facture->pdf_nom_original, 20) }}
                            </a>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($e->type_document)
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $e->type_document === 'VENTE' ? 'bg-green-100 text-green-700' : ($e->type_document === 'ACHAT' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                {{ $e->type_document }}
                            </span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800 whitespace-nowrap">
                            {{ $e->montant_debit > 0 ? number_format((float)$e->montant_debit, 0, ',', ' ') : '' }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800 whitespace-nowrap">
                            {{ $e->montant_credit > 0 ? number_format((float)$e->montant_credit, 0, ',', ' ') : '' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold text-sm border-t border-gray-200">
                    <tr>
                        <td colspan="5" class="px-5 py-3 text-right text-xs uppercase tracking-wide text-gray-500">
                            Totaux (page)
                        </td>
                        <td class="px-5 py-3 text-right text-gray-800">
                            {{ number_format((float)$ecritures->sum('montant_debit'), 0, ',', ' ') }}
                        </td>
                        <td class="px-5 py-3 text-right text-gray-800">
                            {{ number_format((float)$ecritures->sum('montant_credit'), 0, ',', ' ') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $ecritures->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
