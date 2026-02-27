<?php

use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    config(['permission.teams' => true]);
    app(PermissionRegistrar::class)->setPermissionsTeamId(null);
});

test('team resource loads without error', function () {
    $league = League::factory()->create(['slug' => 'team-league']);
    Role::firstOrCreate(['name' => 'league-owner']);

    $user = User::factory()->create();
    $user->leagues()->attach($league);
    app(PermissionRegistrar::class)->setPermissionsTeamId($league->id);
    $user->assignRole('league-owner');

    $this->actingAs($user);

    // Verify the league has teams resource capability
    $this->assertTrue($league->canCreateTeam());

    // Verify teams can be fetched for this league
    $team = Team::factory()->create(['league_id' => $league->id]);
    $teams = Team::where('league_id', $league->id)->get();
    $this->assertCount(1, $teams);
    $this->assertEquals($team->name, $teams->first()->name);
});