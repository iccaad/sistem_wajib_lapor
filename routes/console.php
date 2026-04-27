<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file defines Artisan commands and the task schedule for this
| application. The scheduler runs via:
|
|   Production  → php artisan schedule:run  (cron: * * * * *)
|   Development → php artisan schedule:work (blocks terminal, polls every minute)
|
| Manual runs (for testing):
|   php artisan periods:generate-next
|   php artisan attendance:check-warnings
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// -------------------------------------------------------
// Scheduled Tasks
// -------------------------------------------------------

/**
 * Generate the next attendance period for every participant whose
 * most-recent period ended yesterday. Runs at 00:05 WIB (UTC+7 = 17:05 UTC).
 */
Schedule::command('periods:generate-next')
    ->dailyAt('00:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

/**
 * Check compliance for all active participants and generate warnings
 * (Level 1/2/3) where thresholds have been crossed. Runs at 08:00 WIB.
 */
Schedule::command('attendance:check-warnings')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
