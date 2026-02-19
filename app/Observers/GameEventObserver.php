<?php

namespace App\Observers;

use App\Models\GameEvent;

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
            }
            else {
                $game->home_score += $gameEvent->runs_scored;
            }
            $game->save();
        }

        // 2. Update Player Game Stats (Batter)
        if ($gameEvent->batter_id) {
            $stats = \App\Models\PlayerGameStat::firstOrCreate([
                'game_id' => $gameEvent->game_id,
                'team_id' => $gameEvent->team_id,
                'player_id' => $gameEvent->batter_id,
            ]);

            $this->updateBatterStats($stats, $gameEvent);
            $stats->save();
        }

    // 3. Update Player Game Stats (Pitcher) - TODO
    }

    protected function updateBatterStats($stats, $gameEvent)
    {
        $type = $gameEvent->type;
        $result = $gameEvent->result['kind'] ?? '';

        if ($type === 'play') {
            if ($result === 'out') {
                $stats->ao++;
                $stats->ab++;
            }
            elseif ($result === 'strikeout') {
                $stats->so++;
                $stats->ab++;
            }
            elseif (in_array($result, ['hit', '1b', '2b', '3b', 'hr'])) {
                $stats->h++;
                $stats->ab++;
                // Detailed hits
                if ($result === 'hit')
                    $stats->{ '1b'}++; // Default to single if unspecified
                else
                    $stats->{ $result}++;

                if ($result === 'hr')
                    $stats->r++;
            }
        }
        elseif ($type === 'pitch') {
            if ($result === 'ball') {
            // Logic handled in scoring but here we just react to events.
            // A "walk" event should be explicitly created by the scoring engine
            // If the event type is "walk", then BB++
            }
        }

        $stats->r += $gameEvent->runs_scored;
    // $stats->rbi += ... (needs logic to know who drove in runs)
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