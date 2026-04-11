<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'nom'                => 'Trial',
                'slug'               => 'trial',
                'prix_mensuel_xof'   => 0,
                'quota_factures'     => 10,
                'quota_users'        => 1,
                'duree_essai_jours'  => 14,
                'export_xlsx'        => false,
                'google_sheets'      => false,
                'api_access'         => false,
                'actif'              => true,
                'ordre'              => 0,
            ],
            [
                'nom'                => 'Starter',
                'slug'               => 'starter',
                'prix_mensuel_xof'   => 15000,
                'quota_factures'     => 50,
                'quota_users'        => 2,
                'duree_essai_jours'  => 14,
                'export_xlsx'        => true,
                'google_sheets'      => false,
                'api_access'         => false,
                'actif'              => true,
                'ordre'              => 1,
            ],
            [
                'nom'                => 'Pro',
                'slug'               => 'pro',
                'prix_mensuel_xof'   => 35000,
                'quota_factures'     => 300,
                'quota_users'        => 5,
                'duree_essai_jours'  => 14,
                'export_xlsx'        => true,
                'google_sheets'      => true,
                'api_access'         => false,
                'actif'              => true,
                'ordre'              => 2,
            ],
            [
                'nom'                => 'Cabinet+',
                'slug'               => 'cabinet',
                'prix_mensuel_xof'   => 75000,
                'quota_factures'     => 9999,
                'quota_users'        => 99,
                'duree_essai_jours'  => 30,
                'export_xlsx'        => true,
                'google_sheets'      => true,
                'api_access'         => true,
                'actif'              => true,
                'ordre'              => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        $this->command->info('Plans créés : Trial (gratuit) | Starter 15k | Pro 35k | Cabinet+ 75k FCFA/mois');
    }
}
