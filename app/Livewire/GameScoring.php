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

    public function registerEvent(string $type, string $result)
    {
        // Validation
        if (!$this->batter_id || !$this->pitcher_id) {
             // We can accept some events without this, but for now enforcing it for stats
             // In real app, use Validation
        }

        // Determine Team (Offense)
        $teamId = $this->offensiveTeam->id;
        $defensiveTeamId = $this->defensiveTeam->id;

        // Save Event
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
            'result' => ['kind' => $result],
            'runs_scored' => 0,
        ]);
            'inning' => $this->inning,
            'is_top_inning' => $this->is_top_inning,
            'team_id' => $teamId,
            'batter_id' => $batter?->id,
            'pitcher_id' => $pitcher?->id,
            'outs_before' => $this->outs,
            'balls_before' => $this->balls,
            'strikes_before' => $this->strikes,
            'type' => $type,
            'result' => ['kind' => $result],
            'runs_scored' => 0,
        ]);

        $this->updateState($type, $result);
    }

    protected function updateState($type, $result)
    {
        if ($type === 'pitch') {
            if ($result === 'ball') {
                $this->balls++;
                if ($this->balls >= 4) {
                     // Walk
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
            if ($result === 'out') {
                $this->outs++;
                $this->resetCount();
            } elseif ($result === 'hit') {
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
        if ($this->is_top_inning) {
 