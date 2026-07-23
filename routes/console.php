<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('audits:purge --days=365')->monthly();
Schedule::command('gpi:backup-database')->dailyAt('02:00');
Schedule::command('gpi:remind-pending-approvals')->weekdays()->at('08:30');
Schedule::command('gpi:remind-eod-batch-planning')->weekdays()->everyThirtyMinutes();
