<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        View::addNamespace('layouts', resource_path('views/components/layouts'));

        // Configurar Rate Limiting para APIs y Bruteforce Mitigation
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('global', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(100)->by($request->ip());
        });

        // Observers para cálculo automático de estadísticas
        \App\Models\GameEvent::observe(\App\Observers\GameEventObserver::class);
        \App\Models\Game::observe(\App\Observers\GameObserver::class);
        \App\Models\Player::observe(\App\Observers\PlayerObserver::class);
    }
}
