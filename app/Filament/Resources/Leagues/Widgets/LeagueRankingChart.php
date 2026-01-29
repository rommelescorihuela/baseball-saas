<?php

namespace App\Filament\Resources\Leagues\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\Player;

class LeagueRankingChart extends BarChartWidget
{
    protected ?string $heading = 'Ranking de Jugadores por Liga';

    protected function getData(): array
    {
        $league = current_league();
        $players = Player::with('stats')
            ->whereHas('team', fn($q) => $q->where('league_id', $league->id))
            ->get();

        // Top 10 por Hits
        $players = $players->sortByDesc(fn($p) => $p->stats->sum('hits'))->take(10);

        return [
            'labels' => $players->map(fn($p) => $p->full_name)->toArray(),
            'datasets' => [
                [
                    'label' => 'Hits',
                    'data' => $players->map(fn($p) => $p->stats->sum('hits'))->toArray(),
                ],
            ],
        ];
    }
}
