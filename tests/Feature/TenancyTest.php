<?php

use App\Models\League;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Vite;

beforeEach(function () {
    config(['permission.teams' => true]);
    app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    Vite::spy();
});

test('a user can only access leagues they belong to', function () {
    $league1 = League::factory()->create(['slug' => 'league-1']);
    $league2 = League::factory()->create(['slug' => 'league-2']);

    Role::firstOrCreate(['name' => 'league-owner']);

    $user = User::factory()->create();
    $user->leagues()->attach($league1);

    app(PermissionRegistrar::class)->setPermissionsTeamId($league1->id);
    $user->assignRole('league-owner');

    $this->actingAs($user);

    // Can access league 1
    $this->get("/app/{$league1->slug}")->assertStatus(200);

    // Cannot access league 2 - Filament often returns 404 for tenants the user doesn't belong to
    $this->get("/app/{$league2->slug}")->assertStatus(404);
});