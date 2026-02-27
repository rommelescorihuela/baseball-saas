<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Game;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function show(Competition $competition)
    {
        $competition->load(['teams', 'season']);

        // Caché crítico de 5 min para evitar que cientos de fans tiren la DB al pedir Standings
        $standings = \Illuminate\Support\Facades\Cache::remember('competition_standings_' . $competition->id, 300, function () use ($competition) {
            $calculator = new \App\Services\StandingsCalculator($competition);
            return $calculator->calculate();
        });

        // Attach Team models to standings for display
        $teams = $competition->teams->keyBy('id');

        $standings = $standings->map(function ($standing) use ($teams) {
            $standing['team'] = $teams->get($standing['team_id']);
            return $standing;
        });

        $seoTitle = 'Posiciones Oficiales: ' . $competition->name;
        $seoDescription = 'Sitio oficial de standings y analíticas en vivo para ' . $competition->name . ' - ' . $competition->season->name;
        return view('public.competition.show', compact('competition', 'standings', 'seoTitle', 'seoDescription'));
    }

    public function calendar(Competition $competition)
    {
        $games = Game::whereHas('category', function ($q) use ($competition) {
            $q->where('competition_id', $competition->id);
        })
            ->with(['homeTeam', 'visitorTeam', 'category'])
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        $seoTitle = 'Calendario Oficial: ' . $competition->name;
        return view('public.competition.calendar', compact('competition', 'games', 'seoTitle'));
    }
}