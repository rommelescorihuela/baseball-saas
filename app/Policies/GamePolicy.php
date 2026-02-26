<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GamePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'league_owner', 'secretary', 'coach', 'scorer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Game $game): bool
    {
        if ($user->hasAnyRole(['super_admin', 'league_owner', 'scorer', 'secretary'])) {
            return true;
        }

        if ($user->hasRole('coach')) {
            // Coach can view if their team is playing
            return $user->teams()
                ->where(function ($query) use ($game) {
                    $query->where('teams.id', $game->home_team_id)
                        ->orWhere('teams.id', $game->visitor_team_id);
                })->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'league_owner', 'secretary', 'scorer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Game $game): bool
    {
        if ($user->hasAnyRole(['super_admin', 'league_owner', 'scorer', 'secretary'])) {
            return true;
        }

        if ($user->hasRole('coach')) {
            // Coach can score/update only their own games
            return $user->teams()
                ->where(function ($query) use ($game) {
                    $query->where('teams.id', $game->home_team_id)
                        ->orWhere('teams.id', $game->visitor_team_id);
                })->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Game $game): bool
    {
        return $user->hasAnyRole(['super_admin', 'league_owner']);
    }
}
