<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service FeexPay — Paiements mobile money Bénin
 * Accepte MTN MoMo et Moov Money (FCFA)
 * Documentation : https://docs.feexpay.me
 */
class FeexPayService
{
    private string $token;
    private string $baseUrl = 'https://api.feexpay.me/api';

    public function __construct()
    {
        $this->token = config('services.feexpay.token');
    }

    /**
     * Crée une transaction de paiement et retourne l'URL de paiement.
     *
     * @param int    $montantXof  Montant en FCFA
     * @param string $description Description (ex: "Abonnement Pro — 1 mois")
     * @param string $tenantId    UUID du cabinet
     * @param string $planSlug    Slug du plan (ex: "pro")
     * @param string $redirectUrl URL de retour après paiement réussi
     * @param string $cancelUrl   URL de retour après annulation
     *
     * @return array{succes: bool, payment_url?: string, order_id?: string, erreur?: string}
     */
    public function creerTransaction(
        int    $montantXof,
        string $description,
        string $tenantId,
        string $planSlug,
        string $redirectUrl,
        string $cancelUrl
    ): array {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/orders/online/create", [
                'token'         => $this->token,
                'amount'        => $montantXof,
                'currency'      => 'XOF',
                'description'   => $description,
                // callback_info est retourné par FeexPay dans le webhook — permet l'identification
                'callback_info' => json_encode([
                    'tenant_id' => $tenantId,
                    'plan'      => $planSlug,
                ]),
                'redirect_url'  => $redirectUrl,
                'cancel_url'    => $cancelUrl,
            ]);

            if ($response->successful()) {
                return [
                    'succes'      => true,
                    'payment_url' => $response->json('payment_url'),
                    'order_id'    => $response->json('id'),
                ];
            }

            Log::error('FeexPay: création transaction échouée', [
                'tenant_id'   => $tenantId,
                'plan'        => $planSlug,
                'http_status' => $response->status(),
                'response'    => $response->json(),
            ]);

            return [
                'succes' => false,
                'erreur' => $response->json('message') ?? 'Erreur FeexPay inconnue',
            ];

        } catch (\Exception $e) {
            Log::error('FeexPay: exception', ['error' => $e->getMessage()]);
            return ['succes' => false, 'erreur' => 'Service de paiement indisponible.'];
        }
    }

    /**
     * Vérifie le statut d'une transaction par son ID de commande.
     *
     * @return array Données brutes de la transaction FeexPay
     */
    public function verifierTransaction(string $orderId): array
    {
        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/orders/{$orderId}", [
                'token' => $this->token,
            ]);

            return $response->json() ?? [];

        } catch (\Exception $e) {
            Log::error('FeexPay: vérification transaction échouée', [
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);
            return [];
        }
    }
}
