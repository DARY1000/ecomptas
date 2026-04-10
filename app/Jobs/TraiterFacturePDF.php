<?php

namespace App\Jobs;

use App\Models\Facture;
use App\Models\TraitementLog;
use App\Services\N8nService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job de déclenchement du pipeline n8n.cloud.
 *
 * Queue : database (MySQL) — PAS de Redis sur Hostinger mutualisé.
 * Exécution : cron Hostinger toutes les minutes via
 *   php artisan queue:work --stop-when-empty --max-jobs=5 --max-time=55
 *
 * Logique de retry :
 *  - 3 tentatives max
 *  - Attente de 60 secondes entre chaque tentative
 *  - En cas d'échec définitif → statut "erreur" + log
 */
class TraiterFacturePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;   // 2 min max par job (contrainte mutualisé)
    public int $backoff = 60;    // 1 min entre les tentatives

    public function __construct(public Facture $facture) {}

    public function handle(N8nService $n8n): void
    {
        // Vérifier que la facture est toujours en attente
        $facture = $this->facture->fresh();

        if (!$facture || !in_array($facture->statut, ['uploade', 'erreur'])) {
            Log::info("TraiterFacturePDF: facture {$this->facture->id} déjà traitée — job ignoré");
            return;
        }

        $succes = $n8n->declencherTraitement($facture);

        if (!$succes) {
            // Pas d'exception → le job va se retry automatiquement après $backoff secondes
            Log::warning("TraiterFacturePDF: échec envoi n8n — retry dans {$this->backoff}s", [
                'facture_id' => $facture->id,
                'attempt'    => $this->attempts(),
            ]);

            $this->release($this->backoff);
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->facture->update(['statut' => 'erreur']);

        TraitementLog::create([
            'facture_id' => $this->facture->id,
            'etape'      => 'n8n_envoi',
            'statut'     => 'erreur',
            'message'    => "Échec définitif après {$this->tries} tentatives : {$e->getMessage()}",
        ]);

        Log::error("TraiterFacturePDF: échec définitif", [
            'facture_id' => $this->facture->id,
            'error'      => $e->getMessage(),
        ]);
    }
}
