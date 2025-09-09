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
