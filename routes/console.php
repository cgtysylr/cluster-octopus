<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

app(Schedule::class)
    ->command('kubernetes:check-connections')
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/k8s_check_connections.log'));;
