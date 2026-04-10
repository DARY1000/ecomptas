<?php

namespace App\Services;

use App\Models\Facture;
use App\Models\TraitementLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service de communication avec n8n.cloud
 *
 * Sur Hostinger mutualisé, n8n ne tourne PAS en local — on utilise n8n.cloud
 * (service externe SaaS ~20$/mois). La communication est HTTPS avec :
 *  - Token Bearer (authentification n8n.cloud API)
 *  - Signature HMAC-SHA256 (intégrité du payload)
 *
 * Le PDF est servi via une route Laravel protégée (token signé 30 min)
 * car on ne peut pas générer d'URL MinIO (pas de MinIO sur mutualisé).
 */
class N8nService
{
    private string $webhookUrl;
    private string $apiToken;
    private string $secret;
    private string $callbackUrl;

    public function __construct()
    {
        $this->webhookUrl  = config('services.n8n.webhook_url');
        $this->apiToken    = config('services.n8n.api_token');
        $this->secret      = config('services.n8n.secret');
        $this->callbackUrl = config('services.n8n.callback_url');
    }

    /**
     * Déclenche le pipeline n8n.cloud via webhook HTTPS.
     * Appelé depuis le job TraiterFacturePDF (queue database).
     */
    public function declencherTraitement(Facture $facture): bool
    {
        // Générer une URL temporaire pour servir le PDF à n8n.cloud (30 min)
        // Route Laravel protégée par token chiffré — pas de lien public direct
        $pdfUrl = route('factures.pdf', [
            'facture' => $facture->id,
            'token'   => $this->genererTokenPdf($facture),
        ]);

        $payload = [
            'facture_id'   => $facture->id,
            'tenant_id'    => $facture->tenant_id,
            'pdf_url'      => $pdfUrl,
            'callback_url' => rtrim($this->callbackUrl, '/') . '/webhooks/n8n/callback',
            'timestamp'    => now()->timestamp,
        ];

        // Signature HMAC — vérifiée par n8n (prévient les appels non autorisés)
        $signature = hash_hmac('sha256', json_encode($payload), $this->secret);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'X-N8N-Secret'  => $signature,
                    'X-Tenant-Id'   => $facture->tenant_id,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->webhookUrl, $payload);

            if ($response->successful()) {
                $facture->update([
                    'statut'            => 'traitement_en_cours',
                    'n8n_started_at'    => now(),
                    'n8n_execution_id'  => $response->json('executionId'),
                ]);

                TraitementLog::create([
                    'facture_id' => $facture->id,
                    'etape'      => 'n8n_envoi',
                    'statut'     => 'succes',
                    'message'    => 'Pipeline n8n.cloud déclenché avec succès',
                ]);

                return true;
            }

            Log::error('N8nService: webhook n8n.cloud en échec', [
                'facture_id' => $facture->id,
                'http_status' => $response->status(),
                'response'    => $response->body(),
            ]);

            TraitementLog::create([
                'facture_id' => $facture->id,
                'etape'      => 'n8n_envoi',
                'statut'     => 'erreur',
                'message'    => "HTTP {$response->status()} : {$response->body()}",
            ]);

            return false;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('N8nService: impossible de joindre n8n.cloud', [
                'facture_id' => $facture->id,
                'error'      => $e->getMessage(),
            ]);

            TraitementLog::create([
                'facture_id' => $facture->id,
                'etape'      => 'n8n_envoi',
                'statut'     => 'erreur',
                'message'    => 'Connexion n8n.cloud impossible : ' . $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Vérifie la signature HMAC du callback entrant depuis n8n.cloud.
     * RÈGLE 2 : Refuser 403 si signature invalide ou absente.
     */
    public function verifierSignature(string $payload, string $signature): bool
    {
        if (empty($signature)) return false;

        $expected = hash_hmac('sha256', $payload, $this->secret);
        return hash_equals($expected, $signature);
    }

    /**
     * Génère un token chiffré pour servir le PDF à n8n.cloud (valable 30 min).
     * Stockage local Hostinger — pas d'URL publique directe.
     */
    private function genererTokenPdf(Facture $facture): string
    {
        return encrypt([
            'facture_id' => $facture->id,
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]);
    }
}
