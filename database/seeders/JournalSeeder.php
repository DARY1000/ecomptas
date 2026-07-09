<?php

namespace Database\Seeders;

use App\Models\Journal;
use Illuminate\Database\Seeder;

/**
 * Les 7 journaux SYSCOHADA Révisé standards.
 * tenant_id = null → partagés par tous les cabinets.
 *
 * Usage : php artisan db:seed --class=JournalSeeder
 */
class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $journaux = [
            [
                'code'    => Journal::HA,
                'libelle' => 'Journal des Achats',
                'type'    => 'achat',
            ],
            [
                'code'    => Journal::VE,
                'libelle' => 'Journal des Ventes',
                'type'    => 'vente',
            ],
            [
                'code'    => Journal::BQ,
                'libelle' => 'Journal de Banque',
                'type'    => 'banque',
            ],
            [
                'code'    => Journal::CA,
                'libelle' => 'Journal de Caisse',
                'type'    => 'caisse',
            ],
            [
                'code'    => Journal::OD,
                'libelle' => 'Journal des Opérations Diverses',
                'type'    => 'operations_diverses',
            ],
            [
                'code'    => Journal::AN,
                'libelle' => 'Journal des À-Nouveaux',
                'type'    => 'a_nouveaux',
            ],
            [
                'code'    => Journal::SA,
                'libelle' => 'Journal de Situation (Bilan d\'Ouverture)',
                'type'    => 'situation',
            ],
        ];

        foreach ($journaux as $j) {
            Journal::updateOrCreate(
                ['tenant_id' => null, 'code' => $j['code']],
                array_merge($j, ['actif' => true])
            );
        }

        $this->command->info('7 journaux SYSCOHADA Révisé initialisés (HA, VE, BQ, CA, OD, AN, SA).');
    }
}
