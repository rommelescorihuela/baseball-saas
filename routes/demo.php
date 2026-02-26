<?php

use Illuminate\Support\Facades\Route;

Route::get('/setup-demo-player', function () {
    $player = App\Models\Player::first();
    if (!$player) {
        $team = App\Models\Team::first();
        if (!$team) {
            $league = App\Models\League::first();
            if (!$league) {
                $league = App\Models\League::create(['name' => 'Demo League', 'slug' => 'demo-league', 'subscription_status' => 'active', 'subscription_ends_at' => now()->addYear()]);
            }
            $competition = App\Models\Competition::first();
            if (!$competition) {
                $competition = App\Models\Competition::create(['name' => 'Demo Competition', 'league_id' => $league->id]);
            }
            $team = App\Models\Team::create(['name' => 'Demo Team', 'competition_id' => $competition->id, 'league_id' => $league->id]);
        }
        $player = App\Models\Player::create([
            'team_id' => $team->id,
            'first_name' => 'Demo',
            'last_name' => 'Player',
            'number' => '99',
            'position' => 'CF',
            'status' => 'active',
            'bats' => 'R',
            'throws' => 'R',
            'league_id' => $team->league_id,
        ]);

        // Create sample stats
        $game = App\Models\Game::create([
            'competition_id' => $team->competition_id,
            'home_team_id' => $team->id,
            'away_team_id' => clone $team->id, // Simplified for demo
            'game_date' => now(),
            'status' => 'finished',
            'league_id' => $team->league_id,
        ]);

        App\Models\PlayerGameStat::create([
            'player_id' => $player->id,
            'game_id' => $game->id,
            'team_id' => $team->id,
            'at_bats' => 4,
            'hits' => 2,
            'home_runs' => 1,
            'runs_batted_in' => 3,
            'runs' => 2,
            'strikeouts' => 1,
            'league_id' => $team->league_id,
        ]);
    }

    return response()->json(['player_id' => $player->id, 'league_id' => $player->league_id]);
});
