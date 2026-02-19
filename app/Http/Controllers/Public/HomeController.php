<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Game;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $competitions = Competition::where('is_active', true)->with('season')->get();

        $upcomingGames = Game::where('status', 'scheduled')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(6)
            ->with(['homeTeam', 'visitorTeam', 'category'])
            ->get();

        return view('public.home', compact('competitions', 'upcomingGames'));
    }
}