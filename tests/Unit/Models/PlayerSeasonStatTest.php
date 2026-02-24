<?php

namespace Tests\Unit\Models;

use App\Models\Player;
use App\Models\PlayerSeasonStat;
use App\Models\Season;
use App\Models\Team;

describe('PlayerSeasonStat Model', function () {

    test('can create player season stats', function () {
        $season = Season::factory()->create();
        $player = Player::factory()->create();

        $stats = PlayerSeasonStat::create([
            'season_id' => $season->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => $player->id,
            'g' => 20,
            'ab' => 80,
            'h' => 24,
        ]);

        expect($stats)->toBeInstanceOf(PlayerSeasonStat::class)
            ->and($stats->exists())->toBeTrue();
    });

    test('belongs to a player', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
        ]);

        expect($stats->player)->toBeInstanceOf(Player::class);
    });

    test('belongs to a season', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
        ]);

        expect($stats->season)->toBeInstanceOf(Season::class);
    });

    test('belongs to a team', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
        ]);

        expect($stats->team)->toBeInstanceOf(Team::class);
    });

    test('casts ip as decimal', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 45.2,
        ]);

        expect($stats->ip)->toBe('45.2');
    });
});

describe('PlayerSeasonStat Batting Calculated Attributes', function () {

    test('calculates batting average correctly', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 100,
            'h' => 30,
        ]);

        expect($stats->avg)->toBe('0.300');
    });

    test('returns 000 for batting average with no at bats', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 0,
            'h' => 0,
        ]);

        expect($stats->avg)->toBe('.000');
    });

    test('calculates on-base percentage correctly', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 100,
            'h' => 30,
            'bb' => 20,
            'hbp' => 5,
            'sf' => 5,
        ]);

        // OBP = (H + BB + HBP) / (AB + BB + HBP + SF) = (30+20+5) / (100+20+5+5) = 55/130 = .423
        expect($stats->obp)->toBe('0.423');
    });

    test('returns 000 for slugging with no at bats', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ab' => 0,
            'h' => 0,
        ]);

        expect($stats->slg)->toBe('.000');
    });
});

describe('PlayerSeasonStat Pitching Calculated Attributes', function () {

    test('calculates ERA correctly', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 100,
            'er' => 30,
        ]);

        // ERA = (ER / IP) * 9 = (30/100) * 9 = 2.70
        expect($stats->era)->toBe('2.70');
    });

    test('calculates WHIP correctly', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 100,
            'p_h' => 80,
            'p_bb' => 30,
        ]);

        // WHIP = (H + BB) / IP = (80+30) / 100 = 1.10
        expect($stats->whip)->toBe('1.10');
    });

    test('calculates strikeouts per 9 innings correctly', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 100,
            'p_so' => 120,
        ]);

        // SO/9 = (SO / IP) * 9 = (120/100) * 9 = 10.8
        expect($stats->so9)->toBe('10.8');
    });

    test('calculates walks per 9 innings correctly', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'ip' => 100,
            'p_bb' => 40,
        ]);

        // BB/9 = (BB / IP) * 9 = (40/100) * 9 = 3.6
        expect($stats->bb9)->toBe('3.6');
    });

    test('calculates win-loss record correctly', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'w' => 12,
            'l' => 5,
        ]);

        expect($stats->record)->toBe('12-5');
    });

    test('returns 0.0 for save percentage with no save opportunities', function () {
        $stats = PlayerSeasonStat::create([
            'season_id' => Season::factory()->create()->id,
            'team_id' => Team::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'sv' => 0,
        ]);

        expect($stats->sv_percent)->toBe('0.0');
    });
});
