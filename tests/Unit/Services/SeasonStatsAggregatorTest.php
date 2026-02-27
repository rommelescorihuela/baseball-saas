<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\PlayerSeasonStat;
use App\Models\Season;
use App\Models\Team;
use App\Services\SeasonStatsAggregator;

describe('SeasonStatsAggregator', function () {

    test('can be instantiated', function () {
        $aggregator = app(SeasonStatsAggregator::class);

        expect($aggregator)->toBeInstanceOf(SeasonStatsAggregator::class);
    });

    test('aggregateSeason creates no stats when no finished games', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        // Create a scheduled game (not finished)
        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'status' => 'scheduled',
        ]);

        $aggregator = app(SeasonStatsAggregator::class);
        $aggregator->aggregateSeason($season);

        expect(PlayerSeasonStat::count())->toBe(0);
    });

    test('aggregateSeason aggregates player game stats', function () {
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
            'status' => 'finished',
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
            'r' => 1,
            'rbi' => 2,
        ]);

        $aggregator = app(SeasonStatsAggregator::class);
        $aggregator->aggregateSeason($season);

        $seasonStat = PlayerSeasonStat::where('season_id', $season->id)
            ->where('player_id', $player->id)
            ->first();

        expect($seasonStat)->not->toBeNull()
            ->and($seasonStat->g)->toBe(1)
            ->and($seasonStat->ab)->toBe(4)
            ->and($seasonStat->h)->toBe(2)
            ->and($seasonStat->r)->toBe(1)
            ->and($seasonStat->rbi)->toBe(2);
    });

    test('aggregateSeason sums stats from multiple games', function () {
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

        // Game 1
        $game1 = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'finished',
        ]);

        PlayerGameStat::create([
            'game_id' => $game1->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
            'hr' => 1,
            'r' => 2,
            'rbi' => 3,
        ]);

        // Game 2
        $game2 = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'finished',
        ]);

        PlayerGameStat::create([
            'game_id' => $game2->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 3,
            'h' => 1,
            'hr' => 0,
            'r' => 1,
            'rbi' => 1,
        ]);

        $aggregator = app(SeasonStatsAggregator::class);
        $aggregator->aggregateSeason($season);

        $seasonStat = PlayerSeasonStat::where('season_id', $season->id)
            ->where('player_id', $player->id)
            ->first();

        expect($seasonStat->g)->toBe(2)
            ->and($seasonStat->ab)->toBe(7)
            ->and($seasonStat->h)->toBe(3)
            ->and($seasonStat->hr)->toBe(1)
            ->and($seasonStat->r)->toBe(3)
            ->and($seasonStat->rbi)->toBe(4);
    });

    test('aggregateSeason updates existing stats', function () {
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

        // Create existing season stat
        PlayerSeasonStat::create([
            'season_id' => $season->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'g' => 5,
            'ab' => 20,
            'h' => 6,
        ]);

        $game = Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'finished',
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
        ]);

        $aggregator = app(SeasonStatsAggregator::class);
        $aggregator->aggregateSeason($season);

        $seasonStat = PlayerSeasonStat::where('season_id', $season->id)
            ->where('player_id', $player->id)
            ->first();

        expect($seasonStat->g)->toBe(1)
            ->and($seasonStat->ab)->toBe(4)
            ->and($seasonStat->h)->toBe(2);
    });

    test('aggregatePlayerSeason aggregates stats for specific player', function () {
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
            'status' => 'finished',
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

        $aggregator = app(SeasonStatsAggregator::class);
        $aggregator->aggregatePlayerSeason($season, $player1->id, $team->id);

        // Only player1 should have season stats
        expect(PlayerSeasonStat::where('player_id', $player1->id)->count())->toBe(1)
            ->and(PlayerSeasonStat::where('player_id', $player2->id)->count())->toBe(0);
    });

    test('aggregateSeason handles pitching stats', function () {
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
            'status' => 'finished',
        ]);

        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ip' => 6,
            'p_er' => 2,
            'p_h' => 5,
            'p_bb' => 2,
            'p_so' => 7,
            'w' => 1,
            'l' => 0,
            'sv' => 0,
        ]);

        $aggregator = app(SeasonStatsAggregator::class);
        $aggregator->aggregateSeason($season);

        $seasonStat = PlayerSeasonStat::where('season_id', $season->id)
            ->where('player_id', $player->id)
            ->first();

        expect($seasonStat->ip)->toBe('6.0')
            ->and($seasonStat->p_er)->toBe(2)
            ->and($seasonStat->p_h)->toBe(5)
            ->and($seasonStat->p_bb)->toBe(2)
            ->and($seasonStat->p_so)->toBe(7)
            ->and($seasonStat->w)->toBe(1)
            ->and($seasonStat->l)->toBe(0);
    });

    test('aggregateSeason handles multiple competitions in same season', function () {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);

        $competition1 = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $competition2 = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team = Team::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['league_id' => $league->id]);

        // Game in competition 1
        $game1 = Game::factory()->create([
            'competition_id' => $competition1->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'finished',
        ]);

        PlayerGameStat::create([
            'game_id' => $game1->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
        ]);

        // Game in competition 2
        $game2 = Game::factory()->create([
            'competition_id' => $competition2->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'status' => 'finished',
        ]);

        PlayerGameStat::create([
            'game_id' => $game2->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 3,
            'h' => 1,
        ]);

        $aggregator = app(SeasonStatsAggregator::class);
        $aggregator->aggregateSeason($season);

        $seasonStat = PlayerSeasonStat::where('season_id', $season->id)
            ->where('player_id', $player->id)
            ->first();

        expect($seasonStat->g)->toBe(2)
            ->and($seasonStat->ab)->toBe(7)
            ->and($seasonStat->h)->toBe(3);
    });
});
