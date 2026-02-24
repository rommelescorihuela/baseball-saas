<?php

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\Team;

describe('PlayerGameStat Model', function () {

    test('can create player game stats', function () {
        $game = Game::factory()->create();
        $player = Player::factory()->create(['league_id' => $game->league_id]);

        $stats = PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $game->home_team_id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
        ]);

        expect($stats)->toBeInstanceOf(PlayerGameStat::class)
            ->and($stats->exists())->toBeTrue();
    });

    test('belongs to a player', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
        ]);

        expect($stats->player)->toBeInstanceOf(Player::class);
    });

    test('belongs to a team', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
        ]);

        expect($stats->team)->toBeInstanceOf(Team::class);
    });

    test('belongs to a game', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
        ]);

        expect($stats->game)->toBeInstanceOf(Game::class);
    });

    test('casts ip as decimal', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 5.2,
        ]);

        expect($stats->ip)->toBe('5.2');
    });
});

describe('PlayerGameStat Batting Calculated Attributes', function () {

    test('calculates batting average correctly', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 10,
            'h' => 3,
        ]);

        expect($stats->avg)->toBe('0.300');
    });

    test('returns 000 for batting average with no at bats', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 0,
            'h' => 0,
        ]);

        expect($stats->avg)->toBe('.000');
    });

    test('calculates on-base percentage correctly', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 10,
            'h' => 3,
            'bb' => 2,
            'hbp' => 1,
            'sf' => 1,
        ]);

        // OBP = (H + BB + HBP) / (AB + BB + HBP + SF) = (3+2+1) / (10+2+1+1) = 6/14 = .429
        expect($stats->obp)->toBe('0.429');
    });

    test('returns 000 for OBP with no plate appearances', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 0,
            'h' => 0,
            'bb' => 0,
            'hbp' => 0,
            'sf' => 0,
        ]);

        expect($stats->obp)->toBe('.000');
    });

    test('returns 000 for slugging with no at bats', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 0,
            'h' => 0,
        ]);

        expect($stats->slg)->toBe('.000');
    });
});

describe('PlayerGameStat Pitching Calculated Attributes', function () {

    test('calculates ERA correctly', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 9,
            'er' => 3,
        ]);

        // ERA = (ER / IP) * 9 = (3/9) * 9 = 3.00
        expect($stats->era)->toBe('3.00');
    });

    test('returns 0.00 for ERA with no innings pitched', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 0,
            'er' => 0,
        ]);

        expect($stats->era)->toBe('0.00');
    });

    test('calculates WHIP correctly', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 9,
            'p_h' => 8,
            'p_bb' => 2,
        ]);

        // WHIP = (H + BB) / IP = (8+2) / 9 = 1.11
        expect($stats->whip)->toBe('1.11');
    });

    test('returns 0.00 for WHIP with no innings pitched', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 0,
            'p_h' => 0,
            'p_bb' => 0,
        ]);

        expect($stats->whip)->toBe('0.00');
    });

    test('returns 000 for opponent avg with no innings pitched', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 0,
            'p_h' => 0,
        ]);

        expect($stats->opp_avg)->toBe('.000');
    });

    test('calculates strikeouts per 9 innings correctly', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 6,
            'p_so' => 8,
        ]);

        // SO/9 = (SO / IP) * 9 = (8/6) * 9 = 12.0
        expect($stats->so9)->toBe('12.0');
    });

    test('returns 0.0 for SO/9 with no innings pitched', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 0,
            'p_so' => 0,
        ]);

        expect($stats->so9)->toBe('0.0');
    });

    test('calculates walks per 9 innings correctly', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 6,
            'p_bb' => 3,
        ]);

        // BB/9 = (BB / IP) * 9 = (3/6) * 9 = 4.5
        expect($stats->bb9)->toBe('4.5');
    });

    test('returns 0.0 for BB/9 with no innings pitched', function () {
        $stats = PlayerGameStat::create([
            'game_id' => Game::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 0,
            'p_bb' => 0,
        ]);

        expect($stats->bb9)->toBe('0.0');
    });
});
