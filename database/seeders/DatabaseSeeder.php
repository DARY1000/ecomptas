<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            JournalSeeder::class,          // 7 journaux SYSCOHADA (HA, VE, BQ, CA, OD, AN, SA)
            CompteComptableSeeder::class,  // ~1416 comptes SYSCOHADA Révisé + Bénin
        ]);
    }
}
