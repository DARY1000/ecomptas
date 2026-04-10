<?php

namespace App\Jobs;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Synchronise les écritures d'une facture validée vers Google Sheets.
 * Disponible uniquement pour les plans Pro et Cabinet+.
 * Queue : database — exécuté par le cron Hostinger.
 */
class SyncGoogleSheets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(public Facture $facture) {}

    public function handle(): void
    {
        $facture = $this->facture->fresh();
        $tenant  = $facture->tenant;

        // Vérifier que le tenant a configuré Google Sheets
        $config = $tenant->config_google_sheets;
        if (empty($config['spreadsheet_id']) || empty($config['token_oauth'])) {
            Log::info("SyncGoogleSheets: aucune config GSheets pour tenant {$tenant->id}");
            return;
        }

        try {
            $client = new \Google\Client();
            $client->setAccessToken($config['token_oauth']);

            // Rafraîchir le token si expiré
            if ($client->isAccessTokenExpired() && isset($config['refresh_token'])) {
                $client->fetchAccessTokenWithRefreshToken($config['refresh_token']);
                $newToken = array_merge($config, $client->getAccessToken());
                $tenant->update(['config_google_sheets' => $newToken]);
            }

            $sheetsService = new \Google\Service\Sheets($client);

            // Préparer les lignes à insérer
            $rows = [];
            foreach ($facture->ecritures as $e) {
                $rows[] = [
                    $e->journal,
                    $e->date_ecriture?->format('d/m/Y'),
                    $e->numero_piece,
                    $e->numero_compte,
                    $e->libelle_ecriture,
                    $e->debit > 0 ? (float) $e->debit : '',
                    $e->credit > 0 ? (float) $e->credit : '',
                    $e->devise,
                ];
            }

            $body = new \Google\Service\Sheets\ValueRange(['values' => $rows]);
            $sheetsService->spreadsheets_values->append(
                $config['spreadsheet_id'],
                'Écritures!A:H',
                $body,
                ['valueInputOption' => 'USER_ENTERED']
            );

            Log::info("SyncGoogleSheets: {$facture->id} synchronisé — {$tenant->nom}");

        } catch (\Exception $e) {
            Log::error("SyncGoogleSheets: erreur", [
                'facture_id' => $facture->id,
                'error'      => $e->getMessage(),
            ]);
            // On ne relance pas — la synchro GSheets est optionnelle
        }
    }
}
