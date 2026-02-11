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
        // Standings Logic (simplified for now)
        // In a real scenario, we'd query the standings table or calculate on fly
        // For now, let's just show the teams in the competition
        
        $competition->load(['teams', 'season']);

        return view('public.competition.show', compact('competition'));
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