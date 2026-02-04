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
        
        // Determinar el subdominio o prefijo del host
        $subdomain = $parts[0] ?? '';
        
        // Si estamos en localhost con puerto, extraer el prefijo correctamente
        // Ejemplo: leones.localhost -> subdominio = 'leones'
        // Ejemplo: leones.app.baseball.test -> subdominio = 'leones'
        
        // Evitar tratar 'app' o el dominio principal configurado como equipo
        $mainDomain = config('app.main_domain', 'localhost');
        
        if ($subdomain === 'app' || $subdomain === $mainDomain) {
            // En desarrollo local, buscar equipo por parámetro de ruta
            $teamSlug = $request->route('team');
            
            if ($teamSlug) {
                $team = Team::where('subdomain', $teamSlug)->first();
                if ($team) {
                    app()->instance('currentTeam', $team);
                    app()->instance('currentLeague', $team->league);
                    return $next($request);
                }
            }
            
            app()->instance('currentLeague', null);
            app()->instance('currentTeam', null);
            return $next($request);
        }

        // Buscar equipo por subdominio
        $team = Team::where('subdomain', $subdomain)->first();

        if (!$team) {
            abort(404, 'Equipo no encontrado');
        }

        app()->instance('currentTeam', $team);
        app()->instance('currentLeague', $team->league);

        return $next($request);
    }
}
