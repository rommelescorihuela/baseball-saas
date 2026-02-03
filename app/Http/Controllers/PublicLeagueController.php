<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicLeagueController extends Controller
{
    public function index()
    {
        // 1. Fetch News
        $articles = \App\Models\Article::latest('published_at')->take(5)->get();
        $heroArticle = $articles->first();
        $newsGrid = $articles->skip(1);

        // 2. Calculate Standings
        // This is a naive implementation. In production, consider caching or a dedicated Standings model.
        $teams = \App\Models\Team::all();
        $standings = $teams->map(function ($team) {
            $wins = 0;
            $losses = 0;

            // Home Games
            $homeGames = \App\Models\Game::where('home_team_id', $team->id)->whereNotNull('home_score')->get();
            foreach ($homeGames as $game) {
                if ($game->home_score > $game->away_score)
                    $wins++;
                elseif ($game->home_score < $game->away_score)
                    $losses++;
            }

            // Away Games
            $awayGames = \App\Models\Game::where('away_team_id', $team->id)->whereNotNull('away_score')->get();
            foreach ($awayGames as $game) {
                if ($game->away_score > $game->home_score)
                    $wins++;
                elseif ($game->away_score < $game->home_score)
                    $losses++;
            }

            $total = $wins + $losses;
            $pct = $total > 0 ? $wins / $total : 0;

            return [
                'team' => $team,
                'wins' => $wins,
                'losses' => $losses,
                'pct' => number_format($pct, 3, '.', ''),
                'gb' => '-' // Placeholder
            ];
        })->sortByDesc('pct')->values();

        return view('welcome', compact('heroArticle', 'newsGrid', 'standings'));
    }
}
