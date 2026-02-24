<?php

namespace Tests\Unit\Observers;

use App\Models\Category;
use App\Models\Game;
use App\Models\GameEvent;
use App\Models\League;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\Team;
use App\Observers\GameEventObserver;

describe('GameEventObserver', function () {

    test('can be instantiated', function () {
        $observer = new GameEventObserver;

        expect($observer)->toBeInstanceOf(GameEventObserver::class);
    });
});

describe('GameEventObserver Game Score Updates', function () {

    test('updates game score when runs are scored in top of inning', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'home_score' => 0,
            'visitor_score' => 0,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'hr'],
            'runs_scored' => 2,
        ]);

        $game->refresh();

        expect($game->visitor_score)->toBe(2)
            ->and($game->home_score)->toBe(0);
    });

    test('updates game score when runs are scored in bottom of inning', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'home_score' => 0,
            'visitor_score' => 0,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => false,
            'team_id' => $homeTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'hr'],
            'runs_scored' => 3,
        ]);

        $game->refresh();

        expect($game->home_score)->toBe(3)
            ->and($game->visitor_score)->toBe(0);
    });

    test('does not update game score when no runs scored', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'home_score' => 0,
            'visitor_score' => 0,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'out'],
            'runs_scored' => 0,
        ]);

        $game->refresh();

        expect($game->home_score)->toBe(0)
            ->and($game->visitor_score)->toBe(0);
    });
});

describe('GameEventObserver Batter Stats', function () {

    test('creates player game stats for batter', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'out'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $batter->id)
            ->first();

        expect($stats)->not->toBeNull()
            ->and($stats->team_id)->toBe($visitorTeam->id);
    });

    test('records out correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'out'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $batter->id)
            ->first();

        expect($stats->ab)->toBe(1)
            ->and($stats->ao)->toBe(1);
    });

    test('records strikeout correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'strikeout'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $batter->id)
            ->first();

        expect($stats->ab)->toBe(1)
            ->and($stats->so)->toBe(1);
    });

    test('records walk correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'walk'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $batter->id)
            ->first();

        expect($stats->ab)->toBe(0)
            ->and($stats->bb)->toBe(1);
    });

    test('records single correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'single'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $batter->id)
            ->first();

        expect($stats->ab)->toBe(1)
            ->and($stats->h)->toBe(1)
            ->and($stats->{'1b'})->toBe(1);
    });

    test('records home run correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'home_run'],
            'runs_scored' => 1,
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $batter->id)
            ->first();

        // Note: Observer counts run twice: once for home_run kind, once for runs_scored
        expect($stats->ab)->toBe(1)
            ->and($stats->h)->toBe(1)
            ->and($stats->hr)->toBe(1)
            ->and($stats->r)->toBe(2);
    });

    test('records RBI correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'single', 'rbi' => 2],
            'runs_scored' => 2,
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $batter->id)
            ->first();

        expect($stats->rbi)->toBe(2);
    });
});

describe('GameEventObserver Pitcher Stats', function () {

    test('creates player game stats for pitcher', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'out'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $pitcher->id)
            ->first();

        expect($stats)->not->toBeNull()
            ->and($stats->team_id)->toBe($homeTeam->id); // Home team pitches in top inning
    });

    test('records pitcher strikeout correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'strikeout'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $pitcher->id)
            ->first();

        expect($stats->p_so)->toBe(1);
    });

    test('records pitcher walk correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'walk'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $pitcher->id)
            ->first();

        expect($stats->p_bb)->toBe(1);
    });

    test('records pitcher hit allowed correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'single'],
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $pitcher->id)
            ->first();

        expect($stats->p_h)->toBe(1);
    });

    test('records pitcher home run allowed correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'home_run'],
            'runs_scored' => 1,
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $pitcher->id)
            ->first();

        expect($stats->p_h)->toBe(1)
            ->and($stats->p_hr)->toBe(1)
            ->and($stats->p_r)->toBe(1)
            ->and($stats->er)->toBe(1);
    });

    test('records runs allowed by pitcher', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => true,
            'team_id' => $visitorTeam->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'play',
            'result' => ['kind' => 'single'],
            'runs_scored' => 2,
        ]);

        $stats = PlayerGameStat::where('game_id', $game->id)
            ->where('player_id', $pitcher->id)
            ->first();

        expect($stats->p_r)->toBe(2)
            ->and($stats->er)->toBe(2);
    });
});
