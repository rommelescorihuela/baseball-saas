<?php

namespace App\Filament\Resources\Teams\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Player;

class TeamStatsSummary extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $team = current_team();
        $players = Player::with('stats')->where('team_id', $team->id)->get();

        return [
            Stat::make('Total Players', $players->count()),
            Stat::make('Total Hits', $players->sum(fn($p) => $p->stats->sum('hits'))),
            Stat::make('Total Home Runs', $players->sum(fn($p) => $p->stats->sum('home_runs'))),
            Stat::make('Total RBIs', $players->sum(fn($p) => $p->stats->sum('rbis'))),
        ];
    }
}

