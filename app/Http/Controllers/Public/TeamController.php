<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        $team->load(['players.currentStats', 'homeGames.visitorTeam', 'visitorGames.homeTeam']);

        // Merge games and sort by date
        $games = $team->homeGames->merge($team->visitorGames)->sortBy('start_time');

        $seoTitle = 'Roster Oficial: ' . $team->name;
        $seoDescription = 'Sigue las estadísticas oficiales, listado de jugadores y todo sobre el equipo ' . $team->name;
        $seoImage = $team->logo ? asset('storage/' . $team->logo) : asset('img/diamond-os-banner.jpg');
        return view('public.team.show', compact('team', 'games', 'seoTitle', 'seoDescription', 'seoImage'));
    }
}