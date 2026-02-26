<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::addNamespace('layouts', resource_path('views/components/layouts'));


        // Observers para cálculo automático de estadísticas
        \App\Models\GameEvent::observe(\App\Observers\GameEventObserver::class);
        \App\Models\Game::observe(\App\Observers\GameObserver::class);
    }
}
