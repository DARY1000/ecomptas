<?php

namespace App\Jobs;

use App\Models\ModeleExport;
use App\Services\ModeleExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Analyse IA d'un modèle d'export uploadé par un cabinet — passée en file d'attente
 * (comme TraiterFacturePDF) plutôt qu'exécutée pendant la requête d'upload : l'appel
 * GPT-4o peut prendre plusieurs dizaines de secondes, au-delà du max_execution_time
 * PHP typique sur l'hébergement mutualisé.
 */
class AnalyserModeleExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 90;
    public int $backoff = 30;

    public function __construct(public ModeleExport $modele) {}

    public function handle(ModeleExportService $service): void
    {
        $modele = $this->modele->fresh();

        if (!$modele) {
            return;
        }

        try {
            $service->analyser($modele);
        } catch (\Throwable $e) {
            Log::warning("AnalyserModeleExport: tentative {$this->attempts()} échouée", [
                'modele_id' => $modele->id,
                'error'     => $e->getMessage(),
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
                return;
            }

            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('AnalyserModeleExport: échec définitif', [
            'modele_id' => $this->modele->id,
            'error'     => $e->getMessage(),
        ]);
    }
}
