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

    private function requeteEcritures(
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
