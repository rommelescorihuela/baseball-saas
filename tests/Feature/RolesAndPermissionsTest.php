<?php

use App\Models\User;
use App\Models\League;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Asegurarse de que están limpios los roles
    Role::firstOrCreate(['name' => 'super_admin']);
    Role::firstOrCreate(['name' => 'league_owner']);
    Role::firstOrCreate(['name' => 'team_owner']);
});

test('a user can be assigned a super admin role', function () {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    expect($user->hasRole('super_admin'))->toBeTrue();
});

test('a super admin can access global filament panel', function () {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    actingAs($user)
        ->get('/admin')
        ->assertStatus(200);
});

test('standard user cannot access global filament panel', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/admin')
        ->assertStatus(403);
});

test('league owner has appropriate role assigned', function () {
    $user = User::factory()->create();
    $user->assignRole('league_owner');

    expect($user->hasRole('league_owner'))->toBeTrue();
});
