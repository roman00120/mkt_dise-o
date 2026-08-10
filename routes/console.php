<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reports:run-scheduled')->everyMinute()->withoutOverlapping(10);
Schedule::command('app:backup --all --verify')->dailyAt('02:00')->withoutOverlapping(60);
Schedule::command('app:backup-prune')->dailyAt('03:00')->withoutOverlapping(30);
Schedule::command('storage:prune-temporary')->hourly()->withoutOverlapping(10);
