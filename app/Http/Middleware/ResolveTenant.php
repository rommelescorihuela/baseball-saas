<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Team;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $requestId = substr(md5(uniqid()), 0, 6);
        $request->merge(['_debug_request_id' => $requestId]);

        $host = $request->getHost();
        $parts = explode('.', $host);

        // Determinar el subdominio o prefijo del host
        $subdomain = $parts[0] ?? '';

        \Illuminate\Support\Facades\Log::info("[$requestId] ResolveTenant: Analyzing host", ['host' => $host, 'subdomain' => $subdomain]);

        // Evitar tratar 'app' o el dominio principal configurado como equipo
        $mainDomain = config('app.main_domain', 'localhost');

        if ($subdomain === 'app' || $subdomain === $mainDomain) {
            \Illuminate\Support\Facades\Log::info("[$requestId] ResolveTenant: Main domain detected. No team resolved.");
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
            \Illuminate\Support\Facades\Log::error("[$requestId] ResolveTenant: Team not found for subdomain: " . $subdomain);
            abort(404, 'Equipo no encontrado');
        }

        \Illuminate\Support\Facades\Log::info("[$requestId] ResolveTenant: Team resolved.", ['team_id' => $team->id, 'team_name' => $team->name]);

        app()->instance('currentTeam', $team);
        app()->instance('currentLeague', $team->league);

        return $next($request);
    }
}