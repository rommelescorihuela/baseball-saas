<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Team;
use Livewire\Component;

class GameScoring extends Component
{
    public Game $game;

    // Game State
    public $inning = 1;

    public $is_top_inning = true;

    public $balls = 0;

    public $strikes = 0;

    public $outs = 0;

    // Base runners (corredores en base)
    public $runner_on_first = null;

    public $runner_on_second = null;

    public $runner_on_third = null;

    public $batter_id;

    public $pitcher_id;

    public function mount(Game $game)
    {
        $this->game = $game;

        $lastEvent = $this->game->events()->latest()->first();
        if ($lastEvent) {
            $this->inning = $lastEvent->inning;
            $this->is_top_inning = $lastEvent->is_top_inning;

            // Basic state restoration
            // For now, we assume resets happen on events, so we start fresh or persist counts in DB
        }
    }

    public function getOffensiveTeamProperty()
    {
        return $this->is_top_inning ? $this->game->visitorTeam : $this->game->homeTeam;
    }

    public function getDefensiveTeamProperty()
    {
        return $this->is_top_inning ? $this->game->homeTeam : $this->game->visitorTeam;
    }

    public function render()
    {
        return view('livewire.game-scoring', [
            'offensivePlayers' => $this->offensiveTeam->players,
            'defensivePlayers' => $this->defensiveTeam->players,
        ]);
    }

    /**
     * Finaliza el partido y cambia el status a 'finished'
     * Esto dispara el GameObserver que agrega las estadísticas a la temporada
     */
    public function finishGame()
    {
        if ($this->game->status !== 'finished') {
            $this->game->update(['status' => 'finished']);
            session()->flash('message', 'Partido finalizado. Las estadísticas han sido agregadas a la temporada.');
        }
    }

    /**
     * Registra un evento de juego completo
     */
    public function registerEvent(string $type, string $result, int $rbi = 0)
    {
        // Validation
        if (!$this->batter_id || !$this->pitcher_id) {
            // We can accept some events without this, but for now enforcing it for stats
            // In real app, use Validation
        }

        // Determine Team (Offense)
        $teamId = $this->offensiveTeam->id;
        $defensiveTeamId = $this->defensiveTeam->id;

        // Calculate runs scored based on result and base runners
        $runsScored = $this->calculateRunsScored($type, $result);

        // Save Event with created_by for traceability
        $this->game->events()->create([
            'inning' => $this->inning,
            'is_top_inning' => $this->is_top_inning,
            'team_id' => $teamId,
            'batter_id' => $this->batter_id,
            'pitcher_id' => $this->pitcher_id,
            'outs_before' => $this->outs,
            'balls_before' => $this->balls,
            'strikes_before' => $this->strikes,
            'type' => $type,
            'result' => [
                'kind' => $result,
                'rbi' => $rbi,
                'runners' => [
                    'first' => $this->runner_on_first,
                    'second' => $this->runner_on_second,
                    'third' => $this->runner_on_third,
                ],
            ],
            'runs_scored' => $runsScored,
            'created_by' => auth()->id(), // Trazabilidad: quién registró el evento
        ]);

        $this->updateState($type, $result);
        $this->updateBaseRunners($type, $result);
    }

    /**
     * Calcula las carreras anotadas en una jugada
     */
    protected function calculateRunsScored(string $type, string $result): int
    {
        $runs = 0;

        if ($type === 'play') {
            // Home run: anotan todos los corredores + el bateador
            if ($result === 'hr' || $result === 'home_run') {
                $runs = 1; // El bateador
                if ($this->runner_on_first)
                    $runs++;
                if ($this->runner_on_second)
                    $runs++;
                if ($this->runner_on_third)
                    $runs++;
            }
            // Triple, double, single: puede anotar corredor de tercera
            elseif (in_array($result, ['3b', 'triple'])) {
                if ($this->runner_on_third)
                    $runs++;
                if ($this->runner_on_second)
                    $runs++;
                if ($this->runner_on_first)
                    $runs++;
            } elseif (in_array($result, ['2b', 'double'])) {
                if ($this->runner_on_third)
                    $runs++;
                if ($this->runner_on_second)
                    $runs++;
            } elseif (in_array($result, ['1b', 'single', 'hit', 'error', 'fielders_choice'])) {
                if ($this->runner_on_third)
                    $runs++;
            }
            // Sacrifice fly: anota corredor de tercera
            elseif ($result === 'sacrifice_fly') {
                if ($this->runner_on_third)
                    $runs++;
            }
        }

        return $runs;
    }

    /**
     * Actualiza los corredores en base después de una jugada
     */
    protected function updateBaseRunners(string $type, string $result): void
    {
        if ($type === 'play') {
            // Home run: limpia las bases
            if ($result === 'hr' || $result === 'home_run') {
                $this->runner_on_first = null;
                $this->runner_on_second = null;
                $this->runner_on_third = null;
            }
            // Triple: bateador a tercera
            elseif (in_array($result, ['3b', 'triple'])) {
                $this->runner_on_first = null;
                $this->runner_on_second = null;
                $this->runner_on_third = $this->batter_id;
            }
            // Double: bateador a segunda, corredores avanzan
            elseif (in_array($result, ['2b', 'double'])) {
                $this->runner_on_third = $this->runner_on_second;
                $this->runner_on_second = $this->batter_id;
                $this->runner_on_first = null;
            }
            // Single, Error, Fielder's Choice: bateador a primera, corredores avanzan
            elseif (in_array($result, ['1b', 'single', 'hit', 'error', 'fielders_choice'])) {
                $this->runner_on_third = $this->runner_on_second;
                $this->runner_on_second = $this->runner_on_first;
                $this->runner_on_first = $this->batter_id;
            }
            // Walk: bateador a primera, corredores avanzan forzadamente
            elseif ($result === 'walk' || $result === 'intentional_walk') {
                if ($this->runner_on_first) {
                    if ($this->runner_on_second) {
                        if ($this->runner_on_third) {
                            // Bases llenas, no hay movimiento adicional
                        } else {
                            $this->runner_on_third = $this->runner_on_second;
                        }
                        $this->runner_on_second = $this->runner_on_first;
                    } else {
                        $this->runner_on_second = $this->runner_on_first;
                    }
                }
                $this->runner_on_first = $this->batter_id;
            }
            // Out: el bateador no llega a base
            elseif (in_array($result, ['out', 'strikeout', 'strikeout_looking', 'strikeout_swinging'])) {
                // Los corredores mantienen su posición (a menos que sea fielder's choice, ya manejado arriba)
            }
            // Sacrifice fly: out pero corredor avanza
            elseif ($result === 'sacrifice_fly') {
                if ($this->runner_on_third) {
                    $this->runner_on_third = null; // Anota
                }
            }
        }
    }

    protected function updateState($type, $result)
    {
        if ($type === 'pitch') {
            if ($result === 'ball') {
                $this->balls++;
                if ($this->balls >= 4) {
                    // Walk - se maneja en updateBaseRunners
                    $this->resetCount();
                }
            } elseif ($result === 'strike') {
                $this->strikes++;
                if ($this->strikes >= 3) {
                    // Strikeout
                    $this->outs++;
                    $this->resetCount();
                }
            } elseif ($result === 'foul') {
                if ($this->strikes < 2) {
                    $this->strikes++;
                }
            }
        } elseif ($type === 'play') {
            if (in_array($result, ['out', 'strikeout', 'strikeout_looking', 'strikeout_swinging', 'sacrifice_fly', 'sacrifice_hit'])) {
                $this->outs++;
                $this->resetCount();
            } elseif (in_array($result, ['1b', 'single', '2b', 'double', '3b', 'triple', 'hr', 'home_run', 'hit', 'walk', 'intentional_walk'])) {
                $this->resetCount();
            }
        }

        if ($this->outs >= 3) {
            $this->changeInning();
        }
    }

    protected function resetCount()
    {
        $this->balls = 0;
        $this->strikes = 0;
    }

    protected function changeInning()
    {
        $this->outs = 0;
        $this->resetCount();
        $this->runner_on_first = null;
        $this->runner_on_second = null;
        $this->runner_on_third = null;

        if ($this->is_top_inning) {
            $this->is_top_inning = false;
        } else {
            $this->is_top_inning = true;
            $this->inning++;
        }
    }
}
