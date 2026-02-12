<?php

namespace App\Services;

use App\Models\Competition;
use Illuminate\Support\Collection;

class StandingsCalculator
{
    protected Competition $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function calculate(): Collection
    {
        // Get all finished games for this competition
        $games = $this->competition->games()
            ->where('status', 'finished')
            ->get();

        $standings = collect();

        foreach ($games as $game) {
            // Process Home Team
            $this->updateTeamStats($standings, $game->home_team_id, $game->home_score, $game->visitor_score);

            // Process Visitor Team
            $this->updateTeamStats($standings, $game->visitor_team_id, $game->visitor_score, $game->home_score);
        }

        // Calculate Percentages and Sort
        return $standings->map(function ($stats) {
            $totalGames = $stats['wins'] + $stats['losses'];
            $stats['pct'] = $totalGames > 0 ? round($stats['wins'] / $totalGames, 3) : 0.000;
            return $stats;
        })->sortByDesc('pct')->values();
    }

    protected function updateTeamStats(Collection $standings, int $teamId, int $scoreFor, int $scoreAgainst)
    {
        if (!$standings->has($teamId)) {
            $standings->put($teamId, [
                'team_id' => $teamId,
                'wins' => 0,
                'losses' => 0,
                'runs_for' => 0,
                'runs_against' => 0,
            ]);
        }

        $stats = $standings->get($teamId);
        $stats['runs_for'] += $scoreFor;
        $stats['runs_against'] += $scoreAgainst;

        if ($scoreFor > $scoreAgainst) {
            $stats['wins']++;
        }
        elseif ($scoreFor < $scoreAgainst) {
            $stats['losses']++;
        }
        // Ties handling can be added here if needed

        $standings->put($teamId, $stats);
    }
}