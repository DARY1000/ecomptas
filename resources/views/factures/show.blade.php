@extends('layouts.app')
@section('title', 'Facture — ' . ($facture->numero_facture ?? $facture->pdf_nom_original))

@section('content')
<div class="space-y-5" x-data="{ tab: 'ecritures' }">

    {{-- En-tête --}}
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('factures.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    {{ $facture->numero_facture ?? $facture->pdf_nom_original }}
                </h1>
                @if($facture->fournisseur_client)
                <p class="text-sm text-gray-500 mt-0.5">{{ $facture->fournisseur_client }}</p>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 flex-wrap">
            @php
                $badgeClass = match($facture->statut) {
                    'valide'              => 'bg-green-100 text-green-700 border-green-200',
                    'a_valider'           => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'traitement_en_cours' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'rejete','erreur'     => 'bg-red-100 text-red-700 border-red-200',
                    default               => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $badgeClass }}"
                  id="statut-badge">
                {{ $facture->statut_label }}
            </span>

            @if($facture->statut === 'a_valider' && auth()->user()->peutValider())
                <a href="{{ route('factures.revue', $facture) }}"
                   class="inline-flex items-center gap-2 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Réviser
                </a>
            @endif

            @if($facture->statut === 'a_valider' && auth()->user()->peutValider())
                <form method="POST" action="{{ route('factures.valider', $facture) }}" class="inline"
                      onsubmit="return confirm('Valider cette facture et comptabiliser les écritures ?')">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Valider
                    </button>
                </form>
                <form method="POST" action="{{ route('factures.rejeter', $facture) }}" class="inline"
                      onsubmit="return confirm('Rejeter cette facture ?')">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm font-medium">
                        Rejeter
                    </button>
                </form>
            @endif

            {{-- Voir PDF --}}
            @if($facture->pdf_path)
            <a href="{{ route('factures.pdf', ['facture' => $facture, 'token' => $pdfToken]) }}"
               target="_blank"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Voir PDF
            </a>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Colonne gauche : données facture --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Informations générales --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4 text-sm uppercase tracking-wide">Informations</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Numéro facture</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $facture->numero_facture ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Type de document</dt>
                        <dd class="mt-0.5">
                            @if($facture->type_document)
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $facture->type_document === 'VENTE' ? 'bg-green-100 text-green-700' : ($facture->type_document === 'ACHAT' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                {{ $facture->type_document }}
                            </span>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Fournisseur / Client</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $facture->fournisseur_client ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Date de facture</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $facture->date_facture?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Montant HT</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">
                            {{ $facture->montant_ht ? number_format((float)$facture->montant_ht, 0, ',', ' ') . ' FCFA' : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">TVA (18%)</dt>
                        <dd class="text-gray-700 mt-0.5">
                            {{ $facture->montant_tva ? number_format((float)$facture->montant_tva, 0, ',', ' ') . ' FCFA' : '—' }}
                        </dd>
                    </div>
                    @if($facture->montant_aib)
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">AIB (1%)</dt>
                        <dd class="text-gray-700 mt-0.5">{{ number_format((float)$facture->montant_aib, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    @endif
                    <div class="pt-2 border-t border-gray-100">
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Montant TTC</dt>
                        <dd class="text-xl font-bold text-gray-900 mt-0.5">
                            {{ $facture->montant_ttc ? number_format((float)$facture->montant_ttc, 0, ',', ' ') . ' FCFA' : '—' }}
                        </dd>
                    </div>
                    @if($facture->mode_paiement)
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Mode de paiement</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $facture->mode_paiement }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Statut traitement --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4 text-sm uppercase tracking-wide">Traitement</h2>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Uploadé le</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $facture->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @if($facture->validee_at)
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Validé le</dt>
                        <dd class="text-gray-700 mt-0.5">{{ $facture->validee_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @endif
                    @if($facture->validee_par)
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide">Validé par</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">
                            {{ optional($facture->validateur)->name ?? '—' }}
                        </dd>
                    </div>
                    @endif
                    @if($facture->commentaire_rejet)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-2">
                        <dt class="text-red-600 text-xs font-semibold uppercase tracking-wide mb-1">Motif de rejet</dt>
                        <dd class="text-red-700 text-sm">{{ $facture->commentaire_rejet }}</dd>
                    </div>
                    @endif
                    @if($facture->erreur_message)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-2">
                        <dt class="text-red-600 text-xs font-semibold uppercase tracking-wide mb-1">Erreur</dt>
                        <dd class="text-red-700 text-sm">{{ $facture->erreur_message }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

        </div>

        {{-- Colonne droite : écritures + logs --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- En cours de traitement --}}
            @if($facture->statut === 'traitement_en_cours')
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center" id="processing-banner">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-3">
                    <svg class="animate-spin w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-blue-800 mb-1">Traitement en cours</h3>
                <p class="text-blue-600 text-sm">L'IA analyse votre facture… Veuillez patienter.</p>
            </div>
            @endif

            {{-- Tabs --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100">
                    <nav class="flex">
                        <button type="button"
                                @click="tab = 'ecritures'"
                                :class="tab === 'ecritures' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-5 py-3 text-sm font-medium border-b-2 transition">
                            Écritures comptables
                            @if($facture->ecritures->count() > 0)
                            <span class="ml-1.5 bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full">{{ $facture->ecritures->count() }}</span>
                            @endif
                        </button>
                        <button type="button"
                                @click="tab = 'logs'"
                                :class="tab === 'logs' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-5 py-3 text-sm font-medium border-b-2 transition">
                            Historique
                        </button>
                        @if($facture->donnees_extraites)
                        <button type="button"
                                @click="tab = 'raw'"
                                :class="tab === 'raw' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-5 py-3 text-sm font-medium border-b-2 transition">
                            Données brutes
                        </button>
                        @endif
                    </nav>
                </div>

                {{-- Onglet Écritures --}}
                <div x-show="tab === 'ecritures'" class="p-0">
                    @if($facture->ecritures->isEmpty())
                    <div class="px-6 py-10 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">
                            @if($facture->statut === 'traitement_en_cours')
                                Les écritures seront générées après le traitement IA.
                            @elseif($facture->statut === 'uploade')
                                En attente de traitement.
                            @else
                                Aucune écriture générée.
                            @endif
                        </p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 text-left">N° Compte</th>
                                    <th class="px-4 py-3 text-left">Libellé</th>
                                    <th class="px-4 py-3 text-right">Débit</th>
                                    <th class="px-4 py-3 text-right">Crédit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($facture->ecritures as $e)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2.5">
                                        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded">
                                            {{ $e->numero_compte }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-gray-700">{{ $e->libelle }}</td>
                                    <td class="px-4 py-2.5 text-right font-medium text-gray-800">
                                        {{ $e->montant_debit > 0 ? number_format((float)$e->montant_debit, 0, ',', ' ') : '' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-medium text-gray-800">
                                        {{ $e->montant_credit > 0 ? number_format((float)$e->montant_credit, 0, ',', ' ') : '' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 text-xs font-semibold text-gray-700">
                                <tr>
                                    <td colspan="2" class="px-4 py-2.5 text-right uppercase tracking-wide">Total</td>
                                    <td class="px-4 py-2.5 text-right">
                                        {{ number_format((float)$facture->ecritures->sum('montant_debit'), 0, ',', ' ') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        {{ number_format((float)$facture->ecritures->sum('montant_credit'), 0, ',', ' ') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Équilibre --}}
                    @php $equilibre = abs($facture->ecritures->sum('montant_debit') - $facture->ecritures->sum('montant_credit')) < 1; @endphp
                    <div class="px-4 py-3 border-t border-gray-100">
                        @if($equilibre)
                        <span class="inline-flex items-center gap-1.5 text-green-700 text-xs font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Écriture équilibrée
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-red-600 text-xs font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Écriture déséquilibrée — vérification nécessaire
                        </span>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Onglet Logs --}}
                <div x-show="tab === 'logs'" class="p-5">
                    @if($facture->logs->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-4">Aucun log disponible.</p>
                    @else
                    <ol class="relative border-l border-gray-200 space-y-4 ml-3">
                        @foreach($facture->logs->sortByDesc('created_at') as $log)
                        <li class="ml-4">
                            <span class="absolute flex items-center justify-center w-7 h-7 bg-white border border-gray-200 rounded-full -left-3.5">
                                <span class="text-sm">{{ $log->icone }}</span>
                            </span>
                            <div class="bg-gray-50 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium text-gray-800 text-sm">{{ $log->etape_label }}</span>
                                    <span class="text-xs text-gray-400">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                @if($log->message)
                                <p class="text-sm text-gray-600">{{ $log->message }}</p>
                                @endif
                                @if($log->details)
                                <details class="mt-1">
                                    <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600">Détails techniques</summary>
                                    <pre class="text-xs text-gray-500 mt-1 overflow-x-auto bg-gray-100 rounded p-2">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ol>
                    @endif
                </div>

                {{-- Onglet données brutes --}}
                @if($facture->donnees_extraites)
                <div x-show="tab === 'raw'" class="p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Données extraites par l'IA</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 rounded-lg p-4 overflow-x-auto border border-gray-200">{{ json_encode($facture->donnees_extraites, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                @endif

            </div>
        </div>
    </div>

</div>
@endsection

@if($facture->statut === 'traitement_en_cours')
@push('scripts')
<script>pollStatutFacture('{{ $facture->id }}');</script>
@endpush
@endif
