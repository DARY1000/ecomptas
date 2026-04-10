<?php

namespace Database\Seeders;

use App\Models\CompteComptable;
use Illuminate\Database\Seeder;

/**
 * Plan comptable SYSCOHADA Révisé — comptes utilisés dans l'application.
 * tenant_id = null → comptes globaux partagés par tous les cabinets.
 * Fiscalité Bénin (AIB 1%, RIRF, Régimes B et D) incluse.
 */
class CompteComptableSeeder extends Seeder
{
    public function run(): void
    {
        $comptes = [
            // ── CLASSE 1 — RESSOURCES DURABLES ─────────────────────────
            ['numero' => '101',   'libelle' => 'Capital social',                                  'classe' => '1', 'nature' => 'passif'],

            // ── CLASSE 4 — COMPTES DE TIERS ────────────────────────────
            // Fournisseurs
            ['numero' => '4011',  'libelle' => 'Fournisseurs',                                    'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4012',  'libelle' => 'Fournisseurs Groupe',                             'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4013',  'libelle' => 'Fournisseurs sous-traitants',                     'classe' => '4', 'nature' => 'passif'],
            // Clients
            ['numero' => '4111',  'libelle' => 'Clients',                                         'classe' => '4', 'nature' => 'actif'],
            ['numero' => '4112',  'libelle' => 'Clients - Groupe',                                'classe' => '4', 'nature' => 'actif'],
            ['numero' => '4114',  'libelle' => 'Clients, État et Collectivités publiques',        'classe' => '4', 'nature' => 'actif'],
            // TVA
            ['numero' => '4431',  'libelle' => 'T.V.A. facturée sur ventes',                     'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4432',  'libelle' => 'T.V.A. facturée sur prestations de services',    'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4433',  'libelle' => 'T.V.A. facturée sur travaux',                    'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4441',  'libelle' => 'État, T.V.A. due',                               'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4449',  'libelle' => 'État, crédit de T.V.A. à reporter',              'classe' => '4', 'nature' => 'actif'],
            ['numero' => '4451',  'libelle' => 'T.V.A. récupérable sur immobilisations',        'classe' => '4', 'nature' => 'actif'],
            ['numero' => '4452',  'libelle' => 'T.V.A. récupérable sur achats',                 'classe' => '4', 'nature' => 'actif'],
            ['numero' => '4453',  'libelle' => 'T.V.A. récupérable sur transport',              'classe' => '4', 'nature' => 'actif'],
            ['numero' => '4454',  'libelle' => 'T.V.A. récupérable sur services extérieurs',   'classe' => '4', 'nature' => 'actif'],
            ['numero' => '4455',  'libelle' => 'T.V.A. récupérable sur factures non parvenues', 'classe' => '4', 'nature' => 'actif'],
            // ── Fiscalité spécifique BÉNIN ──────────────────────────
            ['numero' => '4472',  'libelle' => 'AIB collecté 1% (vente)',                        'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4473',  'libelle' => 'RIRF à récupérer',                               'classe' => '4', 'nature' => 'actif'],
            ['numero' => '44593', 'libelle' => 'AIB à récupérer 1% (achat)',                     'classe' => '4', 'nature' => 'actif'],
            // Fournisseurs d'investissement
            ['numero' => '4811',  'libelle' => 'Fournisseurs d\'investissement — immob. incorporelles', 'classe' => '4', 'nature' => 'passif'],
            ['numero' => '4812',  'libelle' => 'Fournisseurs d\'investissement — immob. corporelles',   'classe' => '4', 'nature' => 'passif'],

            // ── CLASSE 5 — TRÉSORERIE ────────────────────────────────
            ['numero' => '5211',  'libelle' => 'Banques en monnaie nationale',                   'classe' => '5', 'nature' => 'actif'],
            ['numero' => '5215',  'libelle' => 'Banques en devises',                             'classe' => '5', 'nature' => 'actif'],
            ['numero' => '5311',  'libelle' => 'Établissements financiers (Mobile Money)',       'classe' => '5', 'nature' => 'actif'],
            ['numero' => '5731',  'libelle' => 'Monnaie électronique (MTN MoMo / Moov Money)',  'classe' => '5', 'nature' => 'actif'],
            ['numero' => '5711',  'libelle' => 'Caisse en monnaie nationale',                   'classe' => '5', 'nature' => 'actif'],
            ['numero' => '5712',  'libelle' => 'Caisse en devises',                             'classe' => '5', 'nature' => 'actif'],

            // ── CLASSE 6 — CHARGES ───────────────────────────────────
            ['numero' => '6011',  'libelle' => 'Achats de marchandises dans la Région',         'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6012',  'libelle' => 'Achats de marchandises hors Région',            'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6021',  'libelle' => 'Achats de matières premières dans la Région',   'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6031',  'libelle' => 'Variations des stocks de marchandises',         'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6041',  'libelle' => 'Matières consommables',                         'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6051',  'libelle' => 'Fournitures non stockables — Eau',              'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6052',  'libelle' => 'Fournitures non stockables — Électricité',     'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6053',  'libelle' => 'Fournitures non stockables — Autres énergies', 'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6057',  'libelle' => 'Achats d\'études et prestations de services',  'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6181',  'libelle' => 'Transports — Voyages et déplacements',         'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6221',  'libelle' => 'Locations de terrains',                        'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6222',  'libelle' => 'Locations de bâtiments',                       'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6228',  'libelle' => 'Locations et charges locatives diverses',      'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6241',  'libelle' => 'Entretien et réparations — biens immobiliers', 'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6242',  'libelle' => 'Entretien et réparations — biens mobiliers',  'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6251',  'libelle' => 'Assurances multirisques',                      'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6271',  'libelle' => 'Publicité — Annonces et insertions',          'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6281',  'libelle' => 'Frais de téléphone',                           'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6288',  'libelle' => 'Autres frais de télécommunications',           'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6317',  'libelle' => 'Frais sur instruments de monnaie électronique','classe' => '6', 'nature' => 'charge'],
            ['numero' => '6318',  'libelle' => 'Autres frais bancaires',                       'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6324',  'libelle' => 'Honoraires des professions règlementées',     'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6351',  'libelle' => 'Cotisations',                                  'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6383',  'libelle' => 'Réceptions',                                   'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6384',  'libelle' => 'Missions',                                     'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6411',  'libelle' => 'Impôts fonciers et taxes annexes',             'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6413',  'libelle' => 'Taxes sur appointements et salaires',          'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6418',  'libelle' => 'Autres impôts et taxes directs',              'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6611',  'libelle' => 'Appointements, salaires et commissions',       'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6641',  'libelle' => 'Charges sociales — personnel national',       'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6812',  'libelle' => 'Dotations aux amortissements — incorporelles', 'classe' => '6', 'nature' => 'charge'],
            ['numero' => '6813',  'libelle' => 'Dotations aux amortissements — corporelles',  'classe' => '6', 'nature' => 'charge'],

            // ── CLASSE 7 — PRODUITS ──────────────────────────────────
            ['numero' => '7011',  'libelle' => 'Ventes de marchandises dans la Région',       'classe' => '7', 'nature' => 'produit'],
            ['numero' => '7012',  'libelle' => 'Ventes de marchandises hors Région',          'classe' => '7', 'nature' => 'produit'],
            ['numero' => '7021',  'libelle' => 'Ventes de produits finis dans la Région',     'classe' => '7', 'nature' => 'produit'],
            ['numero' => '7061',  'libelle' => 'Prestations de services',                      'classe' => '7', 'nature' => 'produit'],
            ['numero' => '7071',  'libelle' => 'Produits des activités annexes',               'classe' => '7', 'nature' => 'produit'],
        ];

        $count = 0;
        foreach ($comptes as $c) {
            CompteComptable::updateOrCreate(
                ['tenant_id' => null, 'numero' => $c['numero']],
                array_merge($c, ['actif' => true])
            );
            $count++;
        }

        $this->command->info("{$count} comptes SYSCOHADA Révisé initialisés (fiscalité Bénin incluse).");
    }
}
