<?php

namespace App\Filament\Resources\Seasons\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Game;

class SeasonOverviewChart extends LineChartWidget
{
    protected ?string $heading = 'Resumen de Temporada';

    protected function getData(): array
    {
        $season = session('current_season');
        $games = Game::with('homeTeam', 'awayTeam')
            ->when($season, fn($q) => $q->where('season_id', $season->id))
            ->get();

        $labels = $games->pluck('game_date')->map(fn($d) => $d->format('d/m'))->toArray();
        $home_scores = $games->pluck('home_score')->toArray();
        $away_scores = $games->pluck('away_score')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Local',
                    'data' => $home_scores,
                ],
                [
                    'label' => 'Visitante',
                    'data' => $away_scores,
                ],
            ],
        ];
    }
}
