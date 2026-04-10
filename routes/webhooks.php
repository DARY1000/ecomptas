<?php

// Routes Webhooks — exclues du CSRF dans bootstrap/app.php
// Sécurité : signature HMAC pour n8n, vérification données pour FeexPay

use App\Http\Controllers\Webhook\N8nCallbackController;
use App\Http\Controllers\Webhook\FeexPayController;
use Illuminate\Support\Facades\Route;

// ── n8n.cloud callback (HTTPS depuis n8n.cloud vers notre serveur) ──
// Sécurisé par HMAC-SHA256 (X-N8N-Secret header)
Route::post('/webhooks/n8n/callback', [N8nCallbackController::class, 'recevoir'])
     ->name('webhooks.n8n');

// ── FeexPay (mobile money Bénin) ────────────────────────────────────
// Sécurisé par token + vérification statut SUCCESSFUL
Route::post('/webhooks/feexpay', [FeexPayController::class, 'callback'])
     ->name('webhooks.feexpay');
