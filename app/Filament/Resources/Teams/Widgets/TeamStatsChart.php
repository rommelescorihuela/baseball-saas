<?php

namespace App\Filament\Resources\Teams\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Player;


class TeamStatsChart extends ChartWidget
{
    protected ?string $heading = 'Estadísticas del Equipo';

    protected function getData(): array
    {
        $team = current_team(); // filtro multitenant
        $players = Player::with('stats')
            ->where('team_id', $team->id)
            ->get();

        return [
            'labels' => $players->map(fn($p) => $p->full_name)->toArray(),
            'datasets' => [
                [
                    'label' => 'Hits',
                    'data' => $players->map(fn($p) => $p->stats->sum('hits'))->toArray(),
                ],
                [
                    'label' => 'Home Runs',
                    'data' => $players->map(fn($p) => $p->stats->sum('home_runs'))->toArray(),
                ],
                [
                    'label' => 'RBIs',
                    'data' => $players->map(fn($p) => $p->stats->sum('rbis'))->toArray(),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
