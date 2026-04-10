<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Queue — Driver DATABASE (Hostinger mutualisé — PAS de Redis)
    |--------------------------------------------------------------------------
    | Sur Hostinger mutualisé, la queue utilise MySQL.
    | Le cron hPanel exécute toutes les minutes :
    |   php artisan queue:work --stop-when-empty --max-jobs=5 --max-time=55
    |
    | --stop-when-empty est CRITIQUE : le processus s'arrête après avoir
    | vidé la queue, respectant la contrainte "pas de process permanent".
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        // Driver database — utilisé en production sur Hostinger
        'database' => [
            'driver'      => 'database',
            'connection'  => env('DB_CONNECTION', 'mysql'),
            'table'       => 'jobs',
            'queue'       => 'default',
            'retry_after' => 180,    // 3 min — après ce délai un job bloqué est relâché
            'after_commit' => false,
        ],

        // Driver array — utilisé dans les tests (pas de persistence)
        'array' => [
            'driver'   => 'array',
            'serialize' => false,
        ],

    ],

    'batching' => [
        'database'  => env('DB_CONNECTION', 'mysql'),
        'table'     => 'job_batches',
    ],

    'failed' => [
        'driver'   => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table'    => 'failed_jobs',
    ],

];
