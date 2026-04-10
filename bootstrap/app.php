<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        then: function () {
            // Charger les routes webhooks séparément (sans CSRF)
            \Illuminate\Support\Facades\Route::middleware('api')
                ->group(base_path('routes/webhooks.php'));
        },
        commands: __DIR__ . '/../routes/console.php',
        health: '/health',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclure les webhooks du CSRF (sécurisés par signature HMAC)
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
            'factures/*/pdf', // Servi à n8n.cloud sans session
        ]);

        // Alias des middleware personnalisés
        $middleware->alias([
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'check.quota'        => \App\Http\Middleware\CheckQuota::class,
            'role'               => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Cron quotidien — expirer les abonnements arrivés à échéance
        $schedule->command('abonnements:expirer')->dailyAt('00:05');

        // Sur Hostinger : la queue est gérée par un cron hPanel séparé (pas ici)
        // Voir la section "Cron Hostinger hPanel" dans PROMPT_COMPTA_SAAS_HOSTINGER.md
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
