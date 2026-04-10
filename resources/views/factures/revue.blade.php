@extends('layouts.app')
@section('title', 'Réviser la facture')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('factures.show', $facture) }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Réviser la facture</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $facture->numero_facture ?? $facture->pdf_nom_original }}
                @if($facture->fournisseur_client) — {{ $facture->fournisseur_client }} @endif
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('factures.valider', $facture) }}">
        @csrf @method('PATCH')

        @if($errors->any())
        <div class="bg-red-50 border border-red-300 rounded-xl px-4 py-3">
            @foreach($errors->all() as $e)
                <p class="text-red-600 text-sm">{{ $e }}</p>
            @endforeach
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Données facture éditables --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Données de la facture
                </h2>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Numéro de facture</label>
                    <input type="text" name="numero_facture"
                           value="{{ old('numero_facture', $facture->numero_facture) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type de document <span class="text-red-500">*</span></label>
                    <select name="type_document"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Sélectionner —</option>
                        <option value="ACHAT" {{ old('type_document', $facture->type_document) === 'ACHAT' ? 'selected' : '' }}>Achat</option>
                        <option value="VENTE" {{ old('type_document', $facture->type_document) === 'VENTE' ? 'selected' : '' }}>Vente</option>
                        <option value="CHARGE" {{ old('type_document', $facture->type_document) === 'CHARGE' ? 'selected' : '' }}>Charge</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fournisseur / Client</label>
                    <input type="text" name="fournisseur_client"
                           value="{{ old('fournisseur_client', $facture->fournisseur_client) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date de facture</label>
                    <input type="date" name="date_facture"
                           value="{{ old('date_facture', $facture->date_facture?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Montant HT (FCFA)</label>
                        <input type="number" name="montant_ht" step="1" min="0"
                               value="{{ old('montant_ht', $facture->montant_ht) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">TVA (FCFA)</label>
                        <input type="number" name="montant_tva" step="1" min="0"
                               value="{{ old('montant_tva', $facture->montant_tva) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">AIB (FCFA)</label>
                        <input type="number" name="montant_aib" step="1" min="0"
                               value="{{ old('montant_aib', $facture->montant_aib) }}"
                               placeholder="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Montant TTC (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="montant_ttc" step="1" min="0" required
                               value="{{ old('montant_ttc', $facture->montant_ttc) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Mode de paiement</label>
                    <select name="mode_paiement"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Sélectionner —</option>
                        <option value="especes" {{ old('mode_paiement', $facture->mode_paiement) === 'especes' ? 'selected' : '' }}>Espèces</option>
                        <option value="virement" {{ old('mode_paiement', $facture->mode_paiement) === 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                        <option value="cheque" {{ old('mode_paiement', $facture->mode_paiement) === 'cheque' ? 'selected' : '' }}>Chèque</option>
                        <option value="mobile_money" {{ old('mode_paiement', $facture->mode_paiement) === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="carte" {{ old('mode_paiement', $facture->mode_paiement) === 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                        <option value="credit" {{ old('mode_paiement', $facture->mode_paiement) === 'credit' ? 'selected' : '' }}>Crédit / À terme</option>
                    </select>
                </div>

            </div>

            {{-- Écritures comptables --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 text-sm uppercase tracking-wide flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Écritures SYSCOHADA générées
                </h2>

                @if($facture->ecritures->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <p class="text-sm">Aucune écriture générée — elles seront créées à la validation.</p>
                </div>
                @else
                <div class="overflow-x-auto -mx-1">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-3 py-2 text-left">Compte</th>
                                <th class="px-3 py-2 text-left">Libellé</th>
                                <th class="px-3 py-2 text-right">Débit</th>
                                <th class="px-3 py-2 text-right">Crédit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($facture->ecritures as $e)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <span class="font-mono bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded">{{ $e->numero_compte }}</span>
                                </td>
                                <td class="px-3 py-2 text-gray-700">{{ Str::limit($e->libelle, 30) }}</td>
                                <td class="px-3 py-2 text-right font-medium text-gray-800">
                                    {{ $e->montant_debit > 0 ? number_format((float)$e->montant_debit, 0, ',', ' ') : '' }}
                                </td>
                                <td class="px-3 py-2 text-right font-medium text-gray-800">
                                    {{ $e->montant_credit > 0 ? number_format((float)$e->montant_credit, 0, ',', ' ') : '' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-semibold text-gray-700">
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-right text-xs uppercase">Total</td>
                                <td class="px-3 py-2 text-right text-xs">
                                    {{ number_format((float)$facture->ecritures->sum('montant_debit'), 0, ',', ' ') }}
                                </td>
                                <td class="px-3 py-2 text-right text-xs">
                                    {{ number_format((float)$facture->ecritures->sum('montant_credit'), 0, ',', ' ') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @php $equilibre = abs($facture->ecritures->sum('montant_debit') - $facture->ecritures->sum('montant_credit')) < 1; @endphp
                <div class="mt-3 flex items-center gap-1.5 text-xs {{ $equilibre ? 'text-green-600' : 'text-red-600' }}">
                    @if($equilibre)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Écriture équilibrée
                    @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Déséquilibre détecté — vérifiez les montants
                    @endif
                </div>
                @endif

                {{-- Commentaire rejet --}}
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Commentaire (optionnel — visible si rejet)
                    </label>
                    <textarea name="commentaire_rejet" rows="3"
                              placeholder="Motif de correction ou observations…"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('commentaire_rejet') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-wrap gap-3 items-center justify-end">
            <a href="{{ route('factures.show', $facture) }}"
               class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                Annuler
            </a>
            <button type="submit" name="action" value="rejeter"
                    onclick="return confirm('Rejeter cette facture ?')"
                    class="px-5 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-medium">
                Rejeter
            </button>
            <button type="submit" name="action" value="valider"
                    onclick="return confirm('Valider et comptabiliser cette facture ?')"
                    class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Valider et comptabiliser
            </button>
        </div>
    </form>

</div>
@endsection
