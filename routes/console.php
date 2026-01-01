<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Feature Flags Scheduler
Schedule::command('features:reset-usage')->daily();
Schedule::command('features:check-alerts')->dailyAt('09:00');
