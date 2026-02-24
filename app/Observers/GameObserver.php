<?php

namespace App\Observers;

use App\Models\Game;
use App\Services\SeasonStatsAggregator;
use Illuminate\Support\Facades\Log;

class GameObserver
{
    /**
     * Handle the Game "updated" event.
     * Cuando un juego cambia a status "finished", agregamos las estadísticas.
     */
    public function updated(Game $game): void
    {
        // Solo cuando el status cambia a 'finished'
        if ($game->isDirty('status') && $game->status === 'finished') {
            $this->aggregateGameStats($game);
        }
    }

    /**
     * Agrega las estadísticas del juego a las estadísticas de temporada
     */
    protected function aggregateGameStats(Game $game): void
    {
        // Obtener la temporada a través de la competencia
        $season = $game->season;

        if (! $season) {
            Log::warning("Game {$game->id} no tiene temporada asociada para agregar estadísticas");

            return;
        }

        $aggregator = app(SeasonStatsAggregator::class);

        // Agregar estadísticas para cada jugador que participó en el juego
        $statsCount = $game->stats()->count();

        if ($statsCount === 0) {
            Log::info("Game {$game->id} no tiene estadísticas de jugadores para agregar");

            return;
        }

        foreach ($game->stats as $playerGameStat) {
            $aggregator->aggregatePlayerSeason(
                $season,
                $playerGameStat->player_id,
                $playerGameStat->team_id
            );
        }

        Log::info("Estadísticas agregadas para {$statsCount} jugadores del juego {$game->id} a la temporada {$season->id}");
    }
}
