<?php

namespace App\Services;

use App\Models\EcritureComptable;
use App\Models\Facture;
use Illuminate\Support\Collection;

/**
 * Service d'export des écritures comptables
 * Génère des fichiers XLSX et CSV directement en PHP
 * Sans dépendance à PhpSpreadsheet (disponible sur mutualisé via maatwebsite/excel)
 */
class ExportService
{
    /**
     * Génère un export CSV des écritures pour un tenant et une période donnée.
     * Format compatible avec les logiciels comptables (Sage, Ciel, etc.)
     */
    public function exportCsv(
        string  $tenantId,
        ?string $dateDebut = null,
        ?string $dateFin   = null,
        ?string $journal   = null
    ): string {
        $ecritures = $this->requeteEcritures($tenantId, $dateDebut, $dateFin, $journal);

        $lignes = [];
        // En-tête
        $lignes[] = implode(';', [
            'Journal', 'Date', 'N° Pièce', 'Compte', 'Libellé compte',
            'Libellé écriture', 'Débit', 'Crédit', 'Devise'
        ]);

        foreach ($ecritures as $e) {
            $lignes[] = implode(';', [
                $e->journal,
                $e->date_ecriture?->format('d/m/Y') ?? '',
                $e->numero_piece ?? '',
                $e->numero_compte,
                $e->libelle_compte ?? '',
                $e->libelle_ecriture,
                $e->debit > 0 ? number_format((float) $e->debit, 2, ',', '') : '',
                $e->credit > 0 ? number_format((float) $e->credit, 2, ',', '') : '',
                $e->devise,
            ]);
        }

        return implode("\n", $lignes);
    }

    /**
     * Génère un fichier XLSX en mémoire via maatwebsite/excel.
     * Retourne le chemin temporaire du fichier.
     */
    public function exportXlsx(
        string  $tenantId,
        ?string $dateDebut = null,
        ?string $dateFin   = null,
        ?string $journal   = null
    ): array {
        $ecritures = $this->requeteEcritures($tenantId, $dateDebut, $dateFin, $journal);

        // Construction du tableau pour l'export
        $data = [
            ['Journal', 'Date', 'N° Pièce', 'Compte', 'Libellé compte', 'Libellé écriture', 'Débit', 'Crédit', 'Devise'],
        ];

        foreach ($ecritures as $e) {
            $data[] = [
                $e->journal,
                $e->date_ecriture?->format('d/m/Y') ?? '',
                $e->numero_piece ?? '',
                $e->numero_compte,
                $e->libelle_compte ?? '',
                $e->libelle_ecriture,
                $e->debit > 0 ? (float) $e->debit : null,
                $e->credit > 0 ? (float) $e->credit : null,
                $e->devise,
            ];
        }

        return $data;
    }

    /**
     * Export au format FEC (Fichier des Écritures Comptables) — colonnes standardisées
     * attendues par l'administration fiscale (JournalCode, EcritureNum, CompAuxNum,
     * ModeRglt, CodeMECeF...). Fichier texte à séparateur tabulation (convention FEC).
     *
     * Limite connue : le règlement est généré dans la même écriture que la facture
     * (pas de suivi séparé des impayés), donc DateRglt/ModeRglt reflètent la ligne
     * de règlement de la facture elle-même, pas un encaissement ultérieur réel.
     */
    public function exportFec(
        string  $tenantId,
        ?string $dateDebut = null,
        ?string $dateFin   = null,
        ?string $journal   = null
    ): string {
        $ecritures = $this->requeteEcritures($tenantId, $dateDebut, $dateFin, $journal)
            ->load('facture');

        $entetes = [
            'JournalCode', 'JournalLib', 'EcritureNum', 'EcritureDate',
            'CompteNum', 'CompteLib', 'CompAuxNum', 'CompAuxLib',
            'PieceRef', 'PieceDate', 'EcritureLib', 'Debit', 'Credit',
            'EcritureLettre', 'DateLettre', 'ValidDate', 'Montantdevise', 'Idevise',
            'DateRglt', 'ModeRglt', 'NatOp', 'IdFournisseur', 'RefMarche', 'CodeMECeF',
        ];

        $lignes   = [implode("\t", $entetes)];
        $compteur = []; // numéro d'écriture séquentiel par journal
        $numeros  = []; // [journal => [facture_id => EcritureNum]] — une écriture = une facture

        foreach ($ecritures as $e) {
            $j = $e->journal;
            if (!isset($numeros[$j][$e->facture_id])) {
                $compteur[$j] = ($compteur[$j] ?? 0) + 1;
                $numeros[$j][$e->facture_id] = $j . str_pad((string) $compteur[$j], 6, '0', STR_PAD_LEFT);
            }

            $facture = $e->facture;

            $lignes[] = implode("\t", [
                $j,
                $this->libelleJournal($j),
                $numeros[$j][$e->facture_id],
                $e->date_ecriture?->format('Ymd') ?? '',
                $this->compteNumFec($e->numero_compte),
                $e->libelle_compte ?? '',
                $facture?->ifu_tiers ?? '',
                $facture?->fournisseur_client ?? '',
                $e->numero_piece ?? '',
                $facture?->date_facture?->format('Ymd') ?? '',
                $e->libelle_ecriture,
                $e->debit > 0 ? number_format((float) $e->debit, 2, '.', '') : '0.00',
                $e->credit > 0 ? number_format((float) $e->credit, 2, '.', '') : '0.00',
                '', // EcritureLettre — lettrage non géré
                '', // DateLettre
                $e->created_at?->format('Ymd') ?? '',
                '', // Montantdevise — laissé vide (devise unique XOF)
                $e->devise,
                $e->est_ecriture_reglement ? $e->date_ecriture?->format('Ymd') : '',
                $e->est_ecriture_reglement ? ($facture?->mode_paiement ?? '') : '',
                $facture?->type_document ?? '',
                '', // IdFournisseur — non distinct de CompAuxNum dans notre modèle actuel
                '', // RefMarche — non applicable
                $facture?->code_mecef ?? '',
            ]);
        }

        return implode("\n", $lignes);
    }

    /**
     * Précision à 5 chiffres attendue dans un FEC (ex. 6011 → 60110) sans toucher au
     * plan comptable interne (resté à 4 chiffres) : ajoute un 0 de sous-compte
     * générique aux comptes généraux à 4 chiffres. Purement cosmétique côté export —
     * ne reproduit pas une éventuelle numérotation analytique propre à un cabinet.
     */
    private function compteNumFec(string $numero): string
    {
        $n = trim($numero);

        return strlen($n) === 4 && ctype_digit($n) ? $n . '0' : $n;
    }

    private function libelleJournal(string $code): string
    {
        return match ($code) {
            'HA' => 'Journal des Achats',
            'VE' => 'Journal des Ventes',
            'BQ' => 'Journal de Banque',
            'CA' => 'Journal de Caisse',
            'OD' => 'Journal des Opérations Diverses',
            'AN' => 'Journal des À-Nouveaux',
            'SA' => 'Journal de Situation',
            default => $code,
        };
    }

    /**
     * Calcul des totaux par journal pour le résumé d'export.
     */
    public function calculerTotaux(string $tenantId, ?string $dateDebut, ?string $dateFin): array
    {
        $ecritures = $this->requeteEcritures($tenantId, $dateDebut, $dateFin);

        return [
            'total_debit'  => $ecritures->sum('debit'),
            'total_credit' => $ecritures->sum('credit'),
            'nb_lignes'    => $ecritures->count(),
            'nb_factures'  => $ecritures->pluck('facture_id')->unique()->count(),
            'equilibre'    => abs($ecritures->sum('debit') - $ecritures->sum('credit')) < 0.01,
        ];
    }

    public function requeteEcritures(
        string  $tenantId,
        ?string $dateDebut = null,
        ?string $dateFin   = null,
        ?string $journal   = null
    ): Collection {
        return EcritureComptable::where('tenant_id', $tenantId)
            ->when($dateDebut, fn($q) => $q->whereDate('date_ecriture', '>=', $dateDebut))
            ->when($dateFin,   fn($q) => $q->whereDate('date_ecriture', '<=', $dateFin))
            ->when($journal,   fn($q) => $q->where('journal', $journal))
            ->orderBy('date_ecriture')
            ->orderBy('facture_id')
            ->orderBy('ordre_ligne')
            ->get();
    }
}
