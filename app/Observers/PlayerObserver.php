<?php

namespace App\Observers;

use App\Models\Player;

class PlayerObserver
{
    /**
     * Handle the Player "creating" event.
     */
    public function creating(Player $player): void
    {
        if ($player->team_id && ! $player->league_id) {
            $player->league_id = $player->team?->league_id;
        }

        if (auth()->check() && ! $player->created_by) {
            $player->created_by = auth()->id();
        }
    }

    /**
     * Handle the Player "updating" event.
     */
    public function updating(Player $player): void
    {
        if ($player->isDirty('team_id') && $player->team_id) {
            $player->league_id = $player->team?->league_id;
        }
    }

    /**
     * Handle the Player "deleted" event.
     */
    public function deleted(Player $player): void
    {
        //
    }

    /**
     * Handle the Player "restored" event.
     */
    public function restored(Player $player): void
    {
        //
    }

    /**
     * Handle the Player "force deleted" event.
     */
    public function forceDeleted(Player $player): void
    {
        //
    }
}
