<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $team = current_team();

        if ($team && $user && $user->team_id !== $team->id) {
            abort(403, 'Acceso no autorizado al equipo');
        }

        return $next($request);
    }
}
