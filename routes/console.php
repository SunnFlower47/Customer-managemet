<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule smart bill generation daily at 9:00 AM
Schedule::command('bills:generate-smart')
    ->dailyAt('09:00')
    ->description('Generate bills based on customer payment dates and check payment status');

// Schedule accurate monthly bill generation on the 1st of every month at 8:00 AM
Schedule::command('bills:generate-monthly-accurate')
    ->monthlyOn(1, '08:00')
    ->description('Generate accurate monthly bills for all active customers');

// Keep the old monthly command as backup
Schedule::command('bills:generate-monthly')
    ->monthlyOn(1, '09:00')
    ->description('Generate monthly bills for all active customers (backup method)');

// OLT Monitoring - Monitor all OLTs every 30 seconds
// Hanya aktif jika OLT_AUTO_SYNC_ENABLED=true di .env (default: true)
if (env('OLT_AUTO_SYNC_ENABLED', true)) {
    Schedule::command('olts:monitor')
        ->everyThirtySeconds()
        ->description('Monitor all OLT devices status');
}

// OLT Sync - Sync database every 5 minutes
// Hanya aktif jika OLT_AUTO_SYNC_ENABLED=true di .env (default: true)
if (env('OLT_AUTO_SYNC_ENABLED', true)) {
    Schedule::command('olts:sync')
        ->everyFiveMinutes()
        ->description('Sync OLT database with devices');
}

// Check unregistered ONUs every 10 minutes
// Hanya aktif jika OLT_AUTO_SYNC_ENABLED=true di .env (default: true)
if (env('OLT_AUTO_SYNC_ENABLED', true)) {
    Schedule::command('olts:check-unregistered')
        ->everyTenMinutes()
        ->description('Check and auto-discover unregistered ONUs');
}
