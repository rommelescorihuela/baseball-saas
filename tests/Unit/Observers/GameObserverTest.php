<?php

namespace Tests\Unit\Observers;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\PlayerSeasonStat;
use App\Models\Season;
use App\Models\Team;
use App\Observers\GameObserver;

describe('GameObserver', function () {

    test('can be instantiated', function () {
        $observer = new GameObserver;

        expect($observer)->toBeInstanceOf(GameObserver::class);
    });

    test('does not aggregate stats when status does not change', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team = Team::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'in_progress',
            'home_score' => 3,
            'visitor_score' => 2,
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
        ]);

        // Update without changing status
        $game->home_score = 5;
        $game->save();

        expect(PlayerSeasonStat::count())->toBe(0);
    });

    test('does not aggregate stats when status changes to non-finished', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team = Team::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'scheduled',
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
        ]);

        // Change status to in_progress
        $game->status = 'in_progress';
        $game->save();

        expect(PlayerSeasonStat::count())->toBe(0);
    });

    test('aggregates stats when status changes to finished', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team = Team::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'in_progress',
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
            'r' => 1,
        ]);

        // Change status to finished
        $game->status = 'finished';
        $game->save();

        $seasonStat = PlayerSeasonStat::where('season_id', $season->id)
            ->where('player_id', $player->id)
            ->first();

        expect($seasonStat)->not->toBeNull()
            ->and($seasonStat->g)->toBe(1)
            ->and($seasonStat->ab)->toBe(4)
            ->and($seasonStat->h)->toBe(2)
            ->and($seasonStat->r)->toBe(1);
    });

    test('does not aggregate when game has no player stats', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team = Team::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'in_progress',
        ]);

        // No player stats created

        // Change status to finished
        $game->status = 'finished';
        $game->save();

        expect(PlayerSeasonStat::count())->toBe(0);
    });

    test('aggregates stats for multiple players', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team = Team::factory()->create(['league_id' => $league->id]);
        $player1 = Player::factory()->create(['league_id' => $league->id]);
        $player2 = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'in_progress',
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player1->id,
            'ab' => 4,
            'h' => 2,
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player2->id,
            'ab' => 3,
            'h' => 1,
        ]);

        // Change status to finished
        $game->status = 'finished';
        $game->save();

        expect(PlayerSeasonStat::where('season_id', $season->id)->count())->toBe(2);
    });
});
