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
        
        $calculator = new \App\Services\StandingsCalculator($competition);
        $standings = $calculator->calculate();

        // Attach Team models to standings for display
        $teams = $competition->teams->keyBy('id');
        
        $standings = $standings->map(function($standing) use ($teams) {
             $standing['team'] = $teams->get($standing['team_id']);
             return $standing;
        });

        return view('public.competition.show', compact('competition', 'standings'));
    }

    public function calendar(Competition $competition)
    {
        $games = Game::whereHas('category', function($q) use ($competition) {
                $q->where('competition_id', $competition->id);
            })
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        return view('public.competition.calendar', compact('competition', 'games'));
    }
}