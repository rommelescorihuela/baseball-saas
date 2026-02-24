<?php

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use App\Models\League;
use App\Models\Season;
use App\Models\Category;
use App\Models\Competition;
use App\Models\PlayerGameStat;
use App\Models\GameEvent;

test('game events correctly update player stats', function () {
    $league = League::factory()->create();
    $season = Season::factory()->create(['league_id' => $league->id]);
    $category = Category::factory()->create(['league_id' => $league->id]);
    $team = Team::factory()->create(['league_id' => $league->id]);
    $competition = Competition::factory()->create([
        'category_id' => $category->id,
        'season_id' => $season->id,
    ]);

    $player = Player::factory()->create();
    $team->players()->attach($player, ['season_id' => $season->id]);

    $game = Game::factory()->create([
        'home_team_id' => $team->id,
        'visitor_team_id' => Team::factory()->create(['league_id' => $league->id])->id,
        'competition_id' => $competition->id,
        'status' => 'in_progress'
    ]);

    // Create a Hit event (Single)
    GameEvent::create([
        'game_id' => $game->id,
        'team_id' => $team->id,
        'batter_id' => $player->id,
        'pitcher_id' => Player::factory()->create()->id,
        'inning' => 1,
        'is_top_inning' => true,
        'type' => 'play',
        'result' => ['kind' => 'hit'],
        'outs_before' => 0,
        'balls_before' => 0,
        'strikes_before' => 0,
        'runs_scored' => 0,
    ]);

    // Verify stats record exists
    $stats = PlayerGameStat::where('player_id', $player->id)->where('game_id', $game->id)->first();

    expect($stats)->not->toBeNull();
    expect($stats->h)->toBe(1);
    expect($stats->ab)->toBe(1);

    // Add an Out (Strikeout)
    GameEvent::create([
        'game_id' => $game->id,
        'team_id' => $team->id,
        'batter_id' => $player->id,
        'pitcher_id' => Player::factory()->create()->id,
        'inning' => 3,
        'is_top_inning' => true,
        'type' => 'play',
        'result' => ['kind' => 'strikeout'],
        'outs_before' => 0,
        'balls_before' => 0,
        'strikes_before' => 0,
        'runs_scored' => 0,
    ]);

    $stats->refresh();
    expect($stats->h)->toBe(1);
    expect($stats->ab)->toBe(2);
    expect($stats->so)->toBe(1);
});