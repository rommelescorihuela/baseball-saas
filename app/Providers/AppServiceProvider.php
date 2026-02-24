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
        \Laravel\Cashier\Cashier::useCustomerModel(\App\Models\League::class);
        \App\Models\GameEvent::observe(\App\Observers\GameEventObserver::class);
    }
}