<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:close-stale-ins', function (\App\Services\AttendanceSessionService $sessions) {
    $n = $sessions->closeAllStaleOpenIns();
    $this->info("Inserted {$n} automatic end-of-day OUT row(s).");

    return 0;
})->purpose('Auto OUT for patrons still IN from a prior calendar day (Asia/Manila).');
