<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Team;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Ejemplo:
        // leones.app.baseball.test
        // [leones, app, baseball, test]

        $subdomain = $parts[0];

        // Evitar tratar 'app' como equipo
        if ($subdomain === 'app') {
            app()->instance('currentLeague', null);
            app()->instance('currentTeam', null);
            return $next($request);
        }

        $team = Team::where('subdomain', $subdomain)->first();

        if (!$team) {
            abort(404, 'Equipo no encontrado');
        }

        app()->instance('currentTeam', $team);
        app()->instance('currentLeague', $team->league);

        return $next($request);
    }
}
