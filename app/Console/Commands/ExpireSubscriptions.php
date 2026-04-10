<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Commande planifiée : expire les abonnements arrivés à échéance.
 * Exécutée chaque nuit via le scheduler Laravel.
 * Sur Hostinger : le cron hPanel exécute "php artisan schedule:run" toutes les minutes.
 */
class ExpireSubscriptions extends Command
{
    protected $signature   = 'abonnements:expirer';
    protected $description = 'Expire les abonnements arrivés à échéance et envoie des rappels J-3';

    public function handle(): int
    {
        $this->info('Vérification des abonnements expirés...');

        // ── 1. Expirer les abonnements dépassés ───────────────────────
        $expires = Tenant::where('statut', 'actif')
            ->where('abonnement_expire_le', '<', now())
            ->get();

        foreach ($expires as $tenant) {
            $tenant->update(['statut' => 'expire']);
            Log::info("Abonnement expiré : {$tenant->nom} ({$tenant->id})");
            $this->line("  Expiré : {$tenant->nom}");
        }

        $this->info("{$expires->count()} abonnement(s) expiré(s).");

        // ── 2. Rappels J-3 (avant expiration) ────────────────────────
        $bientotExpires = Tenant::where('statut', 'actif')
            ->whereBetween('abonnement_expire_le', [now(), now()->addDays(3)])
            ->get();

        foreach ($bientotExpires as $tenant) {
            // TODO : envoyer un email de rappel (implémenté dans Mailable RappelAbonnement)
            // Mail::to($tenant->email_contact)->send(new RappelAbonnementMail($tenant));
            $joursRestants = now()->diffInDays($tenant->abonnement_expire_le);
            $this->line("  Rappel J-{$joursRestants} : {$tenant->nom} — {$tenant->email_contact}");
        }

        $this->info("{$bientotExpires->count()} rappel(s) envoyé(s).");

        return Command::SUCCESS;
    }
}
