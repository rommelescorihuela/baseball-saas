<?php

use App\Models\League;
use App\Models\Player;
use App\Models\Team;

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('a team can be created without a league', function () {
    $team = Team::factory()->create([
        'league_id' => null,
    ]);

    expect($team->league_id)->toBeNull();
    expect($team->exists)->toBeTrue();
});

test('a player created for an independent team has null league_id', function () {
    $team = Team::factory()->create([
        'league_id' => null,
    ]);

    $player = Player::create([
        'team_id' => $team->id,
        'name' => 'Independent Player',
        'number' => 10,
    ]);

    expect($player->league_id)->toBeNull();
    expect($player->team_id)->toBe($team->id);
});

test('a player created for a team with league inherits the league_id', function () {
    $league = League::factory()->create();
    $team = Team::factory()->create([
        'league_id' => $league->id,
    ]);

    $player = Player::create([
        'team_id' => $team->id,
        'name' => 'League Player',
        'number' => 20,
    ]);

    expect($player->league_id)->toBe($league->id);
});
