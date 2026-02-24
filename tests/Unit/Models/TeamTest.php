<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Game;
use App\Models\League;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;

describe('Team Model', function () {

    test('can create a team with factory', function () {
        $team = Team::factory()->create();

        expect($team)->toBeInstanceOf(Team::class)
            ->and($team->exists())->toBeTrue();
    });

    test('has correct fillable attributes', function () {
        $team = new Team;

        expect($team->getFillable())->toContain('name', 'logo', 'league_id');
    });

    test('belongs to a league', function () {
        $team = Team::factory()->create();

        expect($team->league)->toBeInstanceOf(League::class);
    });

    test('belongs to many users', function () {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $team->users()->attach($user);

        expect($team->users)->toHaveCount(1)
            ->and($team->users->first())->toBeInstanceOf(User::class);
    });

    test('belongs to many players through team_player_season', function () {
        $team = Team::factory()->create();
        $league = $team->league;
        $season = Season::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['league_id' => $league->id]);

        $team->players()->attach($player, [
            'season_id' => $season->id,
            'number' => 10,
            'position' => 'P',
        ]);

        expect($team->players)->toHaveCount(1)
            ->and($team->players->first())->toBeInstanceOf(Player::class)
            ->and($team->players->first()->pivot->season_id)->toBe($season->id)
            ->and($team->players->first()->pivot->number)->toBe(10)
            ->and($team->players->first()->pivot->position)->toBe('P');
    });

    test('belongs to many categories', function () {
        $team = Team::factory()->create();
        $category = Category::factory()->create(['league_id' => $team->league_id]);

        $team->categories()->attach($category);

        expect($team->categories)->toHaveCount(1)
            ->and($team->categories->first())->toBeInstanceOf(Category::class);
    });

    test('has many home games', function () {
        $team = Team::factory()->create();
        $game = Game::factory()->create(['home_team_id' => $team->id]);

        expect($team->homeGames)->toHaveCount(1)
            ->and($team->homeGames->first())->toBeInstanceOf(Game::class);
    });

    test('has many visitor games', function () {
        $team = Team::factory()->create();
        $game = Game::factory()->create(['visitor_team_id' => $team->id]);

        expect($team->visitorGames)->toHaveCount(1)
            ->and($team->visitorGames->first())->toBeInstanceOf(Game::class);
    });
});

describe('Team Relationships Integrity', function () {

    test('can have multiple users', function () {
        $team = Team::factory()->create();
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            $team->users()->attach($user);
        }

        expect($team->users)->toHaveCount(3);
    });

    test('can have players in multiple seasons', function () {
        $team = Team::factory()->create();
        $league = $team->league;
        $season1 = Season::factory()->create(['league_id' => $league->id]);
        $season2 = Season::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['league_id' => $league->id]);

        $team->players()->attach($player, [
            'season_id' => $season1->id,
            'number' => 10,
            'position' => 'P',
        ]);

        $team->players()->attach($player, [
            'season_id' => $season2->id,
            'number' => 15,
            'position' => 'C',
        ]);

        expect($team->players)->toHaveCount(2);
    });

    test('can participate in games as home and visitor', function () {
        $team = Team::factory()->create();

        Game::factory()->count(3)->create(['home_team_id' => $team->id]);
        Game::factory()->count(2)->create(['visitor_team_id' => $team->id]);

        expect($team->homeGames)->toHaveCount(3)
            ->and($team->visitorGames)->toHaveCount(2);
    });

    test('deleting team does not delete league', function () {
        $team = Team::factory()->create();
        $leagueId = $team->league_id;

        $team->delete();

        expect(League::find($leagueId))->not->toBeNull();
    });
});
