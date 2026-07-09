<?php

namespace App\Jobs;

use App\Models\Facture;
use App\Models\TraitementLog;
use App\Services\TraitementIAService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job de traitement IA d'une facture.
 * Remplace l'intégration n8n.cloud par un appel direct Mistral OCR + GPT-4o.
 *
 * Queue : database (MySQL) — PAS de Redis sur Hostinger mutualisé.
 * Exécution : cron Hostinger toutes les minutes via
 *   php artisan queue:work --stop-when-empty --max-jobs=5 --max-time=55
 *
 * Logique de retry :
 *  - 3 tentatives max
 *  - Attente de 60 secondes entre chaque tentative (rate-limit API)
 *  - En cas d'échec définitif → statut "erreur" + log
 */
class TraiterFacturePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180;  // 3 min max — OCR + GPT-4o peuvent prendre jusqu'à 2 min
    public int $backoff = 60;   // 1 min entre les tentatives (rate-limit Mistral/OpenAI)

    public function __construct(public Facture $facture) {}

    public function handle(TraitementIAService $ia): void
    {
        // Vérifier que la facture est toujours en attente de traitement
        $facture = $this->facture->fresh();

        if (!$facture || !in_array($facture->statut, ['uploade', 'erreur'])) {
            Log::info("TraiterFacturePDF: facture {$this->facture->id} déjà traitée — job ignoré");
            return;
        }

        try {
            $ia->traiter($facture);
        } catch (\Throwable $e) {
            Log::warning("TraiterFacturePDF: tentative {$this->attempts()} échouée", [
                'facture_id' => $facture->id,
                'error'      => $e->getMessage(),
            ]);

            // Remettre en queue si des tentatives restent
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
                return;
            }

            // Dernier essai échoué → laisser remonter pour déclencher failed()
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->facture->update(['statut' => 'erreur']);

        TraitementLog::create([
            'facture_id' => $this->facture->id,
            'etape'      => 'traitement_ia',
            'statut'     => 'erreur',
            'message'    => "Échec définitif après {$this->tries} tentatives : {$e->getMessage()}",
        ]);

        Log::error("TraiterFacturePDF: échec définitif", [
            'facture_id' => $this->facture->id,
            'error'      => $e->getMessage(),
            'trace'      => $e->getTraceAsString(),
        ]);
    }
}
