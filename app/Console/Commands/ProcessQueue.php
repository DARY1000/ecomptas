<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Commande wrapper pour traiter la queue sur Hostinger mutualisé.
 *
 * Usage dans cron hPanel (toutes les minutes) :
 *   php /home/USERNAME/public_html/artisan queue:process-once
 *
 * Équivalent de :
 *   php artisan queue:work --stop-when-empty --max-jobs=5 --max-time=55 --queue=default
 *
 * --stop-when-empty : CRITIQUE — le processus s'arrête après avoir vidé la queue.
 *                     Respecte la contrainte "pas de processus permanent" du mutualisé.
 * --max-jobs=5      : traite max 5 jobs par exécution cron (évite les timeouts)
 * --max-time=55     : s'arrête avant la prochaine exécution cron (55s < 60s)
 */
class ProcessQueue extends Command
{
    protected $signature   = 'queue:process-once';
    protected $description = 'Traite la queue database une fois (Hostinger cron — stop-when-empty)';

    public function handle(): int
    {
        $this->call('queue:work', [
            '--stop-when-empty' => true,
            '--max-jobs'        => 5,
            '--max-time'        => 55,
            '--queue'           => 'default',
            '--tries'           => 3,
        ]);

        return Command::SUCCESS;
    }
}
