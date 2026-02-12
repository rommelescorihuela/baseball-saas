<?php

use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Vite;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    config(['permission.teams' => true]);
    app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    Vite::spy();
});

test('team resource loads without error', function () {
    $league = League::factory()->create(['slug' => 'team-league']);
    Role::firstOrCreate(['name' => 'league-owner']);

    $user = User::factory()->create();
    $user->leagues()->attach($league);
    app(PermissionRegistrar::class)->setPermissionsTeamId($league->id);
    $user->assignRole('league-owner');

    actingAs($user);

    get("/app/{$league->slug}/teams")
        ->assertStatus(200);
});