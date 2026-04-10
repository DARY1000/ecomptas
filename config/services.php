<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Services tiers — ComptaSaaS
    |--------------------------------------------------------------------------
    */

    // n8n.cloud — Pipeline IA externe (OCR Mistral + Classification GPT-4o-mini)
    // Pas de n8n local sur Hostinger mutualisé — utiliser n8n.cloud (~20$/mois)
    'n8n' => [
        'webhook_url'  => env('N8N_WEBHOOK_URL', 'https://ton-instance.app.n8n.cloud/webhook/traiter-facture'),
        'api_token'    => env('N8N_API_TOKEN'),
        'secret'       => env('N8N_SECRET'),               // Partagé avec n8n pour signature HMAC
        'callback_url' => env('N8N_CALLBACK_URL', 'https://tondomaine.com'),
    ],

    // FeexPay — Mobile money Bénin (MTN MoMo / Moov Money)
    'feexpay' => [
        'token'        => env('FEEXPAY_TOKEN'),
        'callback_url' => env('FEEXPAY_CALLBACK_URL'),
    ],

    // Mistral AI — OCR des factures PDF (utilisé directement dans n8n.cloud)
    'mistral' => [
        'api_key' => env('MISTRAL_API_KEY'),
        'model'   => 'mistral-ocr-latest',
    ],

    // OpenAI — Classification et extraction (utilisé directement dans n8n.cloud)
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model'   => 'gpt-4o-mini',
    ],

    // Google Sheets — Synchronisation des écritures (plan Pro+)
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    // Mail (Mailgun, Postmark, etc. — optionnel)
    'mailgun' => [
        'domain'    => env('MAILGUN_DOMAIN'),
        'secret'    => env('MAILGUN_SECRET'),
        'endpoint'  => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'    => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
