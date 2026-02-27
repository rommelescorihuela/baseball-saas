<?php

namespace Tests\Unit\Models;

use App\Models\League;
use App\Models\Player;
use App\Models\PlayerSeasonStat;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;

describe('Player Model', function () {

    test('can create a player with factory', function () {
        $player = Player::factory()->create();

        expect($player)->toBeInstanceOf(Player::class)
            ->and($player->exists())->toBeTrue();
    });

    test('has correct fillable attributes', function () {
        $player = new Player;

        expect($player->getFillable())->toContain(
            'team_id',
            'league_id',
            'name',
            'last_name',
            'number',
            'date_of_birth',
            'position',
            'created_by'
        );
    });

    test('casts date_of_birth as date', function () {
        $player = Player::factory()->create();

        expect($player->date_of_birth)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('belongs to a league', function () {
        $player = Player::factory()->create();

        expect($player->league)->toBeInstanceOf(League::class);
    });

    test('can exist without a league (academy-centric)', function () {
        $player = Player::factory()->create(['league_id' => null]);
        expect($player->league_id)->toBeNull();
    });

    test('belongs to a team', function () {
        $player = Player::factory()->create();

        expect($player->team)->toBeInstanceOf(Team::class);
    });

    test('belongs to a creator', function () {
        $user = User::factory()->create();
        $player = Player::factory()->create(['created_by' => $user->id]);

        expect($player->creator)->toBeInstanceOf(User::class)
            ->and($player->creator->id)->toBe($user->id);
    });

    test('creator can be null', function () {
        $player = Player::factory()->create(['created_by' => null]);

        expect($player->creator)->toBeNull();
    });

    test('belongs to many teams through team_player_season', function () {
        $player = Player::factory()->create();
        $league = $player->league;
        $season = Season::factory()->create(['league_id' => $league->id]);
        $team = Team::factory()->create(['league_id' => $league->id]);

        $player->teams()->attach($team, [
            'season_id' => $season->id,
            'number' => 23,
            'position' => 'SS',
        ]);

        expect($player->teams)->toHaveCount(1)
            ->and($player->teams->first())->toBeInstanceOf(Team::class)
            ->and($player->teams->first()->pivot->season_id)->toBe($season->id)
            ->and($player->teams->first()->pivot->number)->toBe(23)
            ->and($player->teams->first()->pivot->position)->toBe('SS');
    });

    test('has many stats', function () {
        $player = Player::factory()->create();
        $season = Season::factory()->create(['league_id' => $player->league_id]);

        PlayerSeasonStat::create([
            'player_id' => $player->id,
            'season_id' => $season->id,
            'team_id' => $player->team_id,
            'g' => 10,
            'ab' => 40,
            'h' => 12,
        ]);

        expect($player->stats)->toHaveCount(1)
            ->and($player->stats->first())->toBeInstanceOf(PlayerSeasonStat::class);
    });

    test('has current stats relationship', function () {
        $player = Player::factory()->create();
        $season1 = Season::factory()->create(['league_id' => $player->league_id]);
        $season2 = Season::factory()->create(['league_id' => $player->league_id]);

        // Create older stat
        $olderStat = PlayerSeasonStat::create([
            'player_id' => $player->id,
            'season_id' => $season1->id,
            'team_id' => $player->team_id,
            'g' => 5,
            'created_at' => now()->subDays(10),
        ]);

        // Create newer stat
        $newerStat = PlayerSeasonStat::create([
            'player_id' => $player->id,
            'season_id' => $season2->id,
            'team_id' => $player->team_id,
            'g' => 10,
            'created_at' => now(),
        ]);

        expect($player->currentStats)->toBeInstanceOf(PlayerSeasonStat::class)
            ->and($player->currentStats->id)->toBe($newerStat->id);
    });
});

describe('Player Attributes', function () {

    test('can have various positions', function () {
        $positions = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF'];

        foreach ($positions as $position) {
            $player = Player::factory()->create(['position' => $position]);
            expect($player->position)->toBe($position);
        }
    });

    test('can have jersey number', function () {
        $player = Player::factory()->create(['number' => 99]);

        expect($player->number)->toBe(99);
    });

    test('number can be null', function () {
        $player = Player::factory()->create(['number' => null]);

        expect($player->number)->toBeNull();
    });

    test('date_of_birth can be null', function () {
        $player = Player::factory()->create(['date_of_birth' => null]);

        expect($player->date_of_birth)->toBeNull();
    });
});

describe('Player Relationships Integrity', function () {

    test('deleting player does not delete team', function () {
        $player = Player::factory()->create();
        $teamId = $player->team_id;

        $player->delete();

        expect(Team::find($teamId))->not->toBeNull();
    });

    test('deleting player does not delete league', function () {
        $player = Player::factory()->create();
        $leagueId = $player->league_id;

        $player->delete();

        expect(League::find($leagueId))->not->toBeNull();
    });
});
