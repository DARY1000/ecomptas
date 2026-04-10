<?php

use Illuminate\Support\Facades\Schedule;

// Scheduler Laravel — exécuté via "php artisan schedule:run"
// Sur Hostinger hPanel, ajouter le cron :
//   0 0 * * * php /home/USERNAME/public_html/artisan schedule:run >> /dev/null 2>&1

Schedule::command('abonnements:expirer')->dailyAt('00:05');
