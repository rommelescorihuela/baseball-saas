<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        $team->load(['players.currentStats', 'homeGames.visitorTeam', 'visitorGames.homeTeam']);

        // Merge games and sort by date
        $games = $team->homeGames->merge($team->visitorGames)->sortBy('start_time');

        // Split games into Upcoming and Recent
        $now = Carbon::now();
        $upcomingGames = collect($games)->where('start_time', '>=', $now)->values();
        $recentGames = collect($games)->where('start_time', '<', $now)->sortByDesc('start_time')->take(5)->values();
        $nextGame = $upcomingGames->first();

        // Separate players and their stats logically
        $players = $team->players;
        
        $hitters = $players->filter(function($player) {
            return $player->position !== 'P' && $player->position !== 'RHP' && $player->position !== 'LHP';
        });

        $pitchers = $players->filter(function($player) {
            return in_array($player->position, ['P', 'RHP', 'LHP']);
        });

        // Hitting Leaders
        $leadersBatting = [
            'avg' => $hitters->sortByDesc(fn($p) => (float) ($p->currentStats?->avg ?? 0))->take(3),
            'hr'  => $hitters->sortByDesc(fn($p) => $p->currentStats?->hr ?? 0)->take(3),
            'rbi' => $hitters->sortByDesc(fn($p) => $p->currentStats?->rbi ?? 0)->take(3),
        ];

        // Pitching Leaders
        $leadersPitching = [
            'win' => $pitchers->sortByDesc(fn($p) => $p->currentStats?->w ?? 0)->take(3),
            'era' => $pitchers->filter(fn($p) => ($p->currentStats?->ip ?? 0) > 0)
                              ->sortBy(fn($p) => (float) ($p->currentStats?->era ?? 99.99))->take(3),
            'so'  => $pitchers->sortByDesc(fn($p) => $p->currentStats?->p_so ?? 0)->take(3),
        ];

        // Ensure record
        $wins = collect($games)->where('status', 'finished')->filter(function ($game) use ($team) {
            return ($game->home_team_id === $team->id && $game->home_score > $game->visitor_score) ||
                   ($game->visitor_team_id === $team->id && $game->visitor_score > $game->home_score);
        })->count();

        $losses = collect($games)->where('status', 'finished')->filter(function ($game) use ($team) {
            return ($game->home_team_id === $team->id && $game->home_score < $game->visitor_score) ||
                   ($game->visitor_team_id === $team->id && $game->visitor_score < $game->home_score);
        })->count();
        
        $seasonRecord = "{$wins}-{$losses}";

        $seoTitle = 'Roster Oficial: ' . $team->name;
        $seoDescription = 'Sigue las estadísticas oficiales, listado de jugadores y todo sobre el equipo ' . $team->name;
        $seoImage = $team->logo ? asset('storage/' . $team->logo) : asset('img/diamond-os-banner.jpg');
        
        return view('public.team.show', compact(
            'team', 'games', 'upcomingGames', 'recentGames', 'nextGame',
            'hitters', 'pitchers', 'leadersBatting', 'leadersPitching', 'seasonRecord',
            'seoTitle', 'seoDescription', 'seoImage'
        ));
    }
}