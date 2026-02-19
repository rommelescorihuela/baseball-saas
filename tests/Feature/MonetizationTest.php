<?php

use App\Models\League;
use App\Models\Team;
use App\Models\User;
use App\Enums\Plan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    config(['permission.teams' => true]);
    app(PermissionRegistrar::class)->setPermissionsTeamId(null);
});

test('free plan limits team creation', function () {
    $league = League::factory()->create([
        'plan' => Plan::FREE,
        'slug' => 'free-league'
    ]);

    Role::firstOrCreate(['name' => 'league-owner']);
    app(PermissionRegistrar::class)->setPermissionsTeamId($league->id);

    $user = User::factory()->create();
    $user->assignRole('league-owner');
    $user->leagues()->attach($league);
    $this->actingAs($user);

    // Create 8 teams (the limit)
    Team::factory()->count(8)->create(['league_id' => $league->id]);

    expect($league->canCreateTeam())->toBeFalse();

    // Attempt to access create page should be intercepted by middleware
    $this->get("/app/{$league->slug}/teams/create")
        ->assertStatus(302);
});

test('pro plan allows more teams', function () {
    $league = League::factory()->create([
        'plan' => Plan::PRO,
        'slug' => 'pro-league'
    ]);

    Role::firstOrCreate(['name' => 'league-owner']);
    app(PermissionRegistrar::class)->setPermissionsTeamId($league->id);

    $user = User::factory()->create();
    $user->assignRole('league-owner');
    $user->leagues()->attach($league);
    $this->actingAs($user);

    Team::factory()->count(8)->create(['league_id' => $league->id]);

    expect($league->canCreateTeam())->toBeTrue();

    Team::factory()->count(12)->create(['league_id' => $league->id]);

    expect($league->canCreateTeam())->toBeFalse();

    $this->get("/app/{$league->slug}/teams/create")
        ->assertStatus(302);
});