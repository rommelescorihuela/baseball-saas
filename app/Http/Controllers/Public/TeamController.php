<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        $team->load(['players', 'homeGames.visitorTeam', 'visitorGames.homeTeam']);
        
        // Merge games and sort by date
        $games = $team->homeGames->merge($team->visitorGames)->sortBy('start_time');

        return view('public.team.show', compact('team', 'games'));
    }
}