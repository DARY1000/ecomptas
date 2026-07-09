<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Services tiers — ComptaSaaS
    |--------------------------------------------------------------------------
    */

    // n8n.cloud — LEGACY, non utilisé par le pipeline actuel.
    // Remplacé par TraiterFacturePDF/TraitementIAService (Mistral + OpenAI en direct, voir ci-dessous).
    // Conservé au cas où (webhook + callback encore présents dans routes/webhooks.php).
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

    // Mistral AI — OCR PDF + Vision images (TraitementIAService)
    'mistral' => [
        'api_key'       => env('MISTRAL_API_KEY'),
        'model'         => env('MISTRAL_OCR_MODEL',    'mistral-ocr-latest'),    // OCR PDF
        'vision_model'  => env('MISTRAL_VISION_MODEL', 'pixtral-large-latest'), // Vision JPG/PNG
    ],

    // OpenAI — Classification + Extraction + Écritures (GPT-4o function calling)
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model'   => env('OPENAI_MODEL', 'gpt-4o'),   // gpt-4o = meilleure précision comptable
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
