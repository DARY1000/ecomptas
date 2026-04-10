<?php

namespace App\Services;

use App\Models\Facture;
use App\Models\EcritureComptable;

/**
 * Service de génération des écritures comptables SYSCOHADA Révisé
 *
 * Règles fiscales Bénin implémentées :
 *  - Régime B : TVA 18% applicable → comptes 4431 (vente) / 4452 (achat)
 *  - Régime D : Exonéré de TVA → aucune ligne TVA
 *  - AIB 1% (Acompte sur IS à la Base) → 4472 crédit vente / 44593 débit achat
 *  - RIRF (Retenue IR sur Facture) → 4473 débit vente
 *  - Règlement :
 *      < 100 000 FCFA = Caisse (5711)
 *      ≥ 100 000 FCFA = Banque (5211)
 *      Mobile Money   = 5731 (MTN MoMo / Moov Money)
 */
class SYSCOHADAService
{
    /**
     * Génère les écritures à partir des données retournées par n8n.cloud.
     * Remplace les écritures précédentes si re-génération.
     */
    public function genererEcritures(Facture $facture, array $ecrituresN8n): void
    {
        $facture->ecritures()->delete();
        $ordre = 0;

        foreach ($ecrituresN8n as $ligne) {
            EcritureComptable::create([
                'tenant_id'              => $facture->tenant_id,
                'facture_id'             => $facture->id,
                'journal'                => $this->determinerJournal($facture->type_document),
                'date_ecriture'          => $facture->date_facture ?? today(),
                'numero_piece'           => $facture->numero_facture,
                'numero_compte'          => $ligne['compte'],
                'libelle_compte'         => $ligne['libelle_compte'] ?? $ligne['libelle'] ?? null,
                'libelle_ecriture'       => $ligne['libelle'],
                'debit'                  => (float) ($ligne['debit'] ?? 0),
                'credit'                 => (float) ($ligne['credit'] ?? 0),
                'devise'                 => 'XOF',
                'est_ecriture_reglement' => (bool) ($ligne['est_reglement'] ?? false),
                'ordre_ligne'            => $ordre++,
            ]);
        }
    }

    /**
     * Génère les écritures localement (fallback si n8n ne les fournit pas).
     * Utilisé aussi comme vérification de cohérence.
     */
    public function genererEcrituresManuel(Facture $facture): void
    {
        $lignes = match($facture->type_document) {
            'VENTE'  => $this->ecrituresVente($facture),
            'ACHAT'  => $this->ecrituresAchat($facture),
            'CHARGE' => $this->ecrituresCharge($facture),
            default  => [],
        };

        if (!empty($lignes)) {
            $this->genererEcritures($facture, $lignes);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Génération écritures par type
    // ─────────────────────────────────────────────────────────────────

    private function ecrituresVente(Facture $facture): array
    {
        $ht      = (float) ($facture->montant_ht ?? 0);
        $ttc     = (float) ($facture->montant_ttc ?? 0);
        $tva     = (float) ($facture->montant_tva ?? 0);
        $aib     = (float) ($facture->montant_aib ?? 0);
        $rirf    = (float) ($facture->montant_rirf ?? 0);
        $regimeD = ($facture->regime_fiscal === 'D');

        $lignes = [];

        // DÉBIT — Client (TTC net des retenues)
        $montantClient = $ttc - $aib - $rirf;
        $lignes[] = $this->ligne('4111', 'Clients', $montantClient, 0);

        // DÉBIT — AIB collecté (retenu à la source par le client)
        if ($aib > 0) {
            $lignes[] = $this->ligne('4472', 'AIB collecté 1%', $aib, 0);
        }

        // DÉBIT — RIRF retenu
        if ($rirf > 0) {
            $lignes[] = $this->ligne('4473', 'RIRF à récupérer', $rirf, 0);
        }

        // CRÉDIT — Produits des ventes (HT)
        $lignes[] = $this->ligne('7011', 'Ventes de marchandises', 0, $ht);

        // CRÉDIT — TVA facturée (uniquement régime B)
        if (!$regimeD && $tva > 0) {
            $lignes[] = $this->ligne('4431', 'T.V.A. facturée sur ventes', 0, $tva);
        }

        // Écriture de règlement (trésorerie)
        $lignes[] = $this->ligneReglement($ttc, $facture->mode_paiement, true);

        return $lignes;
    }

    private function ecrituresAchat(Facture $facture): array
    {
        $ht  = (float) ($facture->montant_ht ?? 0);
        $ttc = (float) ($facture->montant_ttc ?? 0);
        $tva = (float) ($facture->montant_tva ?? 0);
        $aib = (float) ($facture->montant_aib ?? 0);

        $lignes = [];

        // DÉBIT — Charge d'achat (HT)
        $lignes[] = $this->ligne('6011', 'Achats de marchandises', $ht, 0);

        // DÉBIT — TVA récupérable sur achats
        if ($tva > 0) {
            $lignes[] = $this->ligne('4452', 'T.V.A. récupérable sur achats', $tva, 0);
        }

        // DÉBIT — AIB à récupérer 1%
        if ($aib > 0) {
            $lignes[] = $this->ligne('44593', 'AIB à récupérer 1%', $aib, 0);
        }

        // CRÉDIT — Fournisseurs (TTC)
        $lignes[] = $this->ligne('4011', 'Fournisseurs', 0, $ttc);

        // Écriture de règlement
        $lignes[] = $this->ligneReglement($ttc, $facture->mode_paiement, false);

        return $lignes;
    }

    private function ecrituresCharge(Facture $facture): array
    {
        $ht      = (float) ($facture->montant_ht ?? 0);
        $ttc     = (float) ($facture->montant_ttc ?? 0);
        $tva     = (float) ($facture->montant_tva ?? 0);
        $donnees = $facture->donnees_extraites ?? [];

        // Le compte de charge est fourni par n8n (classification IA)
        // Fallback : 6228 Locations (charge générique)
        $compteCharge  = $donnees['compte_syscohada'] ?? '6228';
        $libelleCharge = $donnees['libelle_compte'] ?? 'Charges d\'exploitation';

        $lignes = [];
        $lignes[] = $this->ligne($compteCharge, $libelleCharge, $ht, 0);

        if ($tva > 0) {
            $lignes[] = $this->ligne('4454', 'T.V.A. récupérable sur services extérieurs', $tva, 0);
        }

        $lignes[] = $this->ligne('4011', 'Fournisseurs', 0, $ttc);
        $lignes[] = $this->ligneReglement($ttc, $facture->mode_paiement, false);

        return $lignes;
    }

    // ─────────────────────────────────────────────────────────────────
    // Règlement selon mode de paiement et montant (règle Bénin)
    // ─────────────────────────────────────────────────────────────────

    /**
     * RÈGLE 5 — Trésorerie Bénin :
     * - Mobile money (MTN / Moov) → 5731
     * - Montant ≥ 100 000 FCFA    → 5211 Banque
     * - Montant < 100 000 FCFA    → 5711 Caisse
     */
    private function ligneReglement(float $montant, ?string $mode, bool $estVente): array
    {
        [$compte, $libelle] = match(true) {
            $mode === 'mobile_money' => ['5731', 'Monnaie électronique (Mobile Money)'],
            $montant >= 100000       => ['5211', 'Banques en monnaie nationale'],
            default                  => ['5711', 'Caisse en monnaie nationale'],
        };

        return $this->ligne(
            $compte,
            $libelle,
            $estVente ? $montant : 0,
            $estVente ? 0 : $montant,
            true // est_ecriture_reglement
        );
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers privés
    // ─────────────────────────────────────────────────────────────────

    private function ligne(
        string $compte,
        string $libelle,
        float  $debit,
        float  $credit,
        bool   $estReglement = false
    ): array {
        return [
            'compte'          => $compte,
            'libelle_compte'  => $libelle,
            'libelle'         => $libelle,
            'debit'           => $debit,
            'credit'          => $credit,
            'est_reglement'   => $estReglement,
        ];
    }

    private function determinerJournal(?string $type): string
    {
        return match($type) {
            'VENTE'  => 'VTE',
            'ACHAT'  => 'ACH',
            'CHARGE' => 'OD',
            default  => 'OD',
        };
    }
}
