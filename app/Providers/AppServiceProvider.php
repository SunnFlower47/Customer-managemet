<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Route model binding for ODP
        \Illuminate\Support\Facades\Route::bind('odp', function ($value) {
            return \App\Models\Odp::findOrFail($value);
        });

        // Route model binding for OLT
        \Illuminate\Support\Facades\Route::bind('olt', function ($value) {
            return \App\Models\Olt::findOrFail($value);
        });

        // Route model binding for ONU
        \Illuminate\Support\Facades\Route::bind('onu', function ($value) {
            return \App\Models\Onu::findOrFail($value);
        });

        // Route model binding for VLAN
        \Illuminate\Support\Facades\Route::bind('vlan', function ($value) {
            return \App\Models\VlanDatabase::findOrFail($value);
        });

        // Route model binding for SpeedProfile
        \Illuminate\Support\Facades\Route::bind('speedProfile', function ($value) {
            return \App\Models\SpeedProfile::findOrFail($value);
        });
    }
}
