<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\League $league */
        $league = \Filament\Facades\Filament::getTenant();

        if (!$league) {
            return $next($request);
        }

        // Check for Team creation
        if ($request->is('*/teams/create*') && !$league->canCreateTeam()) {
            \Filament\Notifications\Notification::make()
                ->title('Límite de Equipos Superado')
                ->body('Tu plan actual (' . $league->plan->label() . ') no permite crear más equipos.')
                ->danger()
                ->send();

            return redirect()->back();
        }

        // Check for Competition creation
        if ($request->is('*/competitions/create*') && !$league->canCreateCompetition()) {
            \Filament\Notifications\Notification::make()
                ->title('Límite de Competiciones Superado')
                ->body('Tu plan actual (' . $league->plan->label() . ') no permite crear más competiciones.')
                ->danger()
                ->send();

            return redirect()->back();
        }

        return $next($request);
    }
}