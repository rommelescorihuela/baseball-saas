<?php

namespace App\Observers;

use App\Models\GameEvent;
use App\Models\PlayerGameStat;

class GameEventObserver
{
    /**
     * Handle the GameEvent "created" event.
     */
    public function created(GameEvent $gameEvent): void
    {
        // 1. Update Game Score (if runs scored)
        if ($gameEvent->runs_scored > 0) {
            $game = $gameEvent->game;
            if ($gameEvent->is_top_inning) {
                $game->visitor_score += $gameEvent->runs_scored;
            } else {
                $game->home_score += $gameEvent->runs_scored;
            }
            $game->save();
        }

        // 2. Update Player Game Stats (Batter)
        if ($gameEvent->batter_id) {
            $stats = PlayerGameStat::firstOrCreate([
                'game_id' => $gameEvent->game_id,
                'team_id' => $gameEvent->team_id,
                'player_id' => $gameEvent->batter_id,
            ]);

            $this->updateBatterStats($stats, $gameEvent);
            $stats->save();
        }

        // 3. Update Player Game Stats (Pitcher)
        if ($gameEvent->pitcher_id) {
            $pitcherStats = PlayerGameStat::firstOrCreate([
                'game_id' => $gameEvent->game_id,
                'team_id' => $gameEvent->is_top_inning ? $gameEvent->game->home_team_id : $gameEvent->game->visitor_team_id,
                'player_id' => $gameEvent->pitcher_id,
            ]);

            $this->updatePitcherStats($pitcherStats, $gameEvent);
            $pitcherStats->save();
        }
    }

    /**
     * Actualiza estadísticas del bateador
     */
    protected function updateBatterStats(PlayerGameStat $stats, GameEvent $gameEvent): void
    {
        $type = $gameEvent->type;
        $result = $gameEvent->result['kind'] ?? '';
        $rbi = $gameEvent->result['rbi'] ?? 0;

        if ($type === 'play') {
            switch ($result) {
                case 'out':
                    $stats->ao++;
                    $stats->ab++;
                    break;

                case 'strikeout':
                case 'strikeout_looking':
                case 'strikeout_swinging':
                    $stats->so++;
                    $stats->ab++;
                    break;

                case 'walk':
                case 'intentional_walk':
                    $stats->bb++;
                    // No cuenta como At Bat
                    break;

                case 'hit_by_pitch':
                    $stats->hbp++;
                    break;

                case 'sacrifice_fly':
                    $stats->sac++;
                    // No cuenta como At Bat
                    break;

                case 'sacrifice_hit':
                    $stats->sf++;
                    // No cuenta como At Bat
                    break;

                case '1b':
                case 'single':
                    $stats->h++;
                    $stats->ab++;
                    $stats->{'1b'}++;
                    break;

                case '2b':
                case 'double':
                    $stats->h++;
                    $stats->ab++;
                    $stats->{'2b'}++;
                    break;

                case '3b':
                case 'triple':
                    $stats->h++;
                    $stats->ab++;
                    $stats->{'3b'}++;
                    break;

                case 'hr':
                case 'home_run':
                    $stats->h++;
                    $stats->ab++;
                    $stats->hr++;
                    $stats->r++; // El bateador anota carrera
                    break;

                case 'hit':
                    // Hit genérico, contar como single
                    $stats->h++;
                    $stats->ab++;
                    $stats->{'1b'}++;
                    break;
            }

            // RBI (Carreras Impulsadas)
            if ($rbi > 0) {
                $stats->rbi += $rbi;
            }
        }

        // Carreras anotadas por el evento
        if ($gameEvent->runs_scored > 0) {
            $stats->r += $gameEvent->runs_scored;
        }
    }

    /**
     * Actualiza estadísticas del pitcher
     */
    protected function updatePitcherStats(PlayerGameStat $stats, GameEvent $gameEvent): void
    {
        $type = $gameEvent->type;
        $result = $gameEvent->result['kind'] ?? '';

        if ($type === 'play') {
            switch ($result) {
                case 'strikeout':
                case 'strikeout_looking':
                case 'strikeout_swinging':
                    $stats->p_so++;
                    // Out para el pitcher
                    $this->recordOut($stats, $gameEvent);
                    break;

                case 'walk':
                case 'intentional_walk':
                    $stats->p_bb++;
                    break;

                case 'hit_by_pitch':
                    $stats->p_hbp++;
                    break;

                case '1b':
                case 'single':
                case 'hit':
                    $stats->p_h++;
                    break;

                case '2b':
                case 'double':
                    $stats->p_h++;
                    break;

                case '3b':
                case 'triple':
                    $stats->p_h++;
                    break;

                case 'hr':
                case 'home_run':
                    $stats->p_h++;
                    $stats->p_hr++;
                    break;

                case 'out':
                    $this->recordOut($stats, $gameEvent);
                    break;

                case 'sacrifice_fly':
                    $stats->sac++;
                    $this->recordOut($stats, $gameEvent);
                    break;

                case 'sacrifice_hit':
                    $stats->sf++;
                    $this->recordOut($stats, $gameEvent);
                    break;
            }

            // Carreras permitidas
            if ($gameEvent->runs_scored > 0) {
                $stats->p_r += $gameEvent->runs_scored;
                // TODO: Determinar si es carrera limpia (earned run)
                // Por ahora asumimos que todas son limpias
                $stats->er += $gameEvent->runs_scored;
            }
        }

        // Wild pitch
        if ($type === 'pitch' && $result === 'wild_pitch') {
            $stats->p_wp++;
        }

        // Balk
        if ($type === 'pitch' && $result === 'balk') {
            $stats->p_bk++;
        }
    }

    /**
     * Registra un out y actualiza innings pitched
     */
    protected function recordOut(PlayerGameStat $stats, GameEvent $gameEvent): void
    {
        // Cada out representa 1/3 de inning
        // Usamos decimal: 0.1 = 1 out, 0.2 = 2 outs, 1.0 = 3 outs (1 inning completo)
        $currentIp = (float) $stats->ip;
        $outs = (int) (($currentIp - floor($currentIp)) * 10); // Extraer los décimos

        $outs++;
        if ($outs >= 3) {
            // Completó un inning
            $stats->ip = floor($currentIp) + 1.0;
        } else {
            $stats->ip = floor($currentIp) + ($outs / 10);
        }
    }

    /**
     * Handle the GameEvent "updated" event.
     */
    public function updated(GameEvent $gameEvent): void
    {
        //
    }

    /**
     * Handle the GameEvent "deleted" event.
     */
    public function deleted(GameEvent $gameEvent): void
    {
        //
    }

    /**
     * Handle the GameEvent "restored" event.
     */
    public function restored(GameEvent $gameEvent): void
    {
        //
    }

    /**
     * Handle the GameEvent "force deleted" event.
     */
    public function forceDeleted(GameEvent $gameEvent): void
    {
        //
    }
}
