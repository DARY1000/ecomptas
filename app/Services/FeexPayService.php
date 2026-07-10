<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service FeexPay — Paiements mobile money / carte (MTN, Moov, VISA, Mastercard...)
 * Documentation : https://docs.feexpay.me
 *
 * Le paiement lui-même passe par le widget FeexPay (bouton + modale hébergée par
 * FeexPay, voir abonnement/index.blade.php) — ce service ne sert qu'à vérifier
 * côté serveur le statut réel d'une transaction avant d'activer un abonnement,
 * sans jamais faire confiance aveuglément au webhook ou à un paramètre d'URL.
 */
class FeexPayService
{
    private string $token;
    private string $baseUrl = 'https://api-v2.feexpay.me/api';

    public function __construct()
    {
        $this->token = config('services.feexpay.token', '');
    }

    /**
     * Vérifie le statut réel d'une transaction auprès de FeexPay par sa référence.
     * status attendu : PENDING | SUCCESSFUL | FAILED.
     *
     * @return array Données brutes de la transaction FeexPay (vide si erreur/injoignable)
     */
    public function verifierTransaction(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])
            ->timeout(15)
            ->get("{$this->baseUrl}/transactions/public/single/status/{$reference}");

            if (!$response->successful()) {
                Log::error('FeexPay: vérification transaction — HTTP ' . $response->status(), [
                    'reference' => $reference,
                    'body'      => $response->body(),
                ]);
                return [];
            }

            return $response->json() ?? [];

        } catch (\Exception $e) {
            Log::error('FeexPay: vérification transaction échouée', [
                'reference' => $reference,
                'error'     => $e->getMessage(),
            ]);
            return [];
        }
    }
}
