<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\GameEvent;
use App\Models\League;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\Season;
use App\Models\Team;

describe('Game Model', function () {

    test('can create a game with factory', function () {
        $game = Game::factory()->create();

        expect($game)->toBeInstanceOf(Game::class)
            ->and($game->exists())->toBeTrue();
    });

    test('has correct fillable attributes', function () {
        $game = new Game;

        expect($game->getFillable())->toContain(
            'category_id',
            'league_id',
            'competition_id',
            'home_team_id',
            'visitor_team_id',
            'start_time',
            'location',
            'status',
            'home_score',
            'visitor_score'
        );
    });

    test('casts start_time as datetime', function () {
        $game = Game::factory()->create();

        expect($game->start_time)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('belongs to a competition', function () {
        $game = Game::factory()->create();

        expect($game->competition)->toBeInstanceOf(Competition::class);
    });

    test('belongs to a league', function () {
        $game = Game::factory()->create();

        expect($game->league)->toBeInstanceOf(League::class);
    });

    test('belongs to a category', function () {
        $game = Game::factory()->create();

        expect($game->category)->toBeInstanceOf(Category::class);
    });

    test('belongs to home team', function () {
        $game = Game::factory()->create();

        expect($game->homeTeam)->toBeInstanceOf(Team::class);
    });

    test('belongs to visitor team', function () {
        $game = Game::factory()->create();

        expect($game->visitorTeam)->toBeInstanceOf(Team::class);
    });

    test('has many events', function () {
        $game = Game::factory()->create();
        $batter = Player::factory()->create(['league_id' => $game->league_id]);
        $pitcher = Player::factory()->create(['league_id' => $game->league_id]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $game->home_team_id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'out'],
        ]);

        expect($game->events)->toHaveCount(1)
            ->and($game->events->first())->toBeInstanceOf(GameEvent::class);
    });

    test('has many stats', function () {
        $game = Game::factory()->create();
        $player = Player::factory()->create(['league_id' => $game->league_id]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $game->home_team_id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
        ]);

        expect($game->stats)->toHaveCount(1)
            ->and($game->stats->first())->toBeInstanceOf(PlayerGameStat::class);
    });

    test('gets season through competition', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'season_id' => $season->id,
            'category_id' => $category->id,
            'league_id' => $league->id,
        ]);
        $game = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
        ]);

        expect($game->season)->toBeInstanceOf(Season::class)
            ->and($game->season->id)->toBe($season->id);
    });
});

describe('Game Status Methods', function () {

    test('isFinished returns true when status is finished', function () {
        $game = Game::factory()->create(['status' => 'finished']);

        expect($game->isFinished())->toBeTrue();
    });

    test('isFinished returns false when status is not finished', function () {
        $game = Game::factory()->create(['status' => 'scheduled']);

        expect($game->isFinished())->toBeFalse();
    });

    test('isInProgress returns true when status is in_progress', function () {
        $game = Game::factory()->create(['status' => 'in_progress']);

        expect($game->isInProgress())->toBeTrue();
    });

    test('isInProgress returns false when status is not in_progress', function () {
        $game = Game::factory()->create(['status' => 'finished']);

        expect($game->isInProgress())->toBeFalse();
    });

    test('isPending returns false when status is scheduled', function () {
        $game = Game::factory()->create(['status' => 'scheduled']);

        // Note: isPending checks for 'pending' or null, not 'scheduled'
        expect($game->isPending())->toBeFalse();
    });

    test('isPending returns false when status is not scheduled or null', function () {
        $game = Game::factory()->create(['status' => 'finished']);

        expect($game->isPending())->toBeFalse();
    });
});
