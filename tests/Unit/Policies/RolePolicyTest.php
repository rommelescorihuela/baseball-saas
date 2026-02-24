<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\RolePolicy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

describe('RolePolicy', function () {

    test('can be instantiated', function () {
        $policy = new RolePolicy;

        expect($policy)->toBeInstanceOf(RolePolicy::class);
    });

    test('viewAny returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'ViewAny:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $policy = new RolePolicy;

        expect($policy->viewAny($user))->toBeTrue();
    });

    test('viewAny returns false when user lacks permission', function () {
        $user = User::factory()->create();

        $policy = new RolePolicy;

        expect($policy->viewAny($user))->toBeFalse();
    });

    test('view returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'View:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->view($user, $role))->toBeTrue();
    });

    test('view returns false when user lacks permission', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'test-role-2', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->view($user, $role))->toBeFalse();
    });

    test('create returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'Create:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $policy = new RolePolicy;

        expect($policy->create($user))->toBeTrue();
    });

    test('create returns false when user lacks permission', function () {
        $user = User::factory()->create();

        $policy = new RolePolicy;

        expect($policy->create($user))->toBeFalse();
    });

    test('update returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'Update:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $role = Role::create(['name' => 'test-role-3', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->update($user, $role))->toBeTrue();
    });

    test('update returns false when user lacks permission', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'test-role-4', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->update($user, $role))->toBeFalse();
    });

    test('delete returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'Delete:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $role = Role::create(['name' => 'test-role-5', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->delete($user, $role))->toBeTrue();
    });

    test('delete returns false when user lacks permission', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'test-role-6', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->delete($user, $role))->toBeFalse();
    });

    test('restore returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'Restore:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $role = Role::create(['name' => 'test-role-7', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->restore($user, $role))->toBeTrue();
    });

    test('restore returns false when user lacks permission', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'test-role-8', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->restore($user, $role))->toBeFalse();
    });

    test('forceDelete returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'ForceDelete:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $role = Role::create(['name' => 'test-role-9', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->forceDelete($user, $role))->toBeTrue();
    });

    test('forceDelete returns false when user lacks permission', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'test-role-10', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->forceDelete($user, $role))->toBeFalse();
    });

    test('forceDeleteAny returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'ForceDeleteAny:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $policy = new RolePolicy;

        expect($policy->forceDeleteAny($user))->toBeTrue();
    });

    test('forceDeleteAny returns false when user lacks permission', function () {
        $user = User::factory()->create();

        $policy = new RolePolicy;

        expect($policy->forceDeleteAny($user))->toBeFalse();
    });

    test('restoreAny returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'RestoreAny:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $policy = new RolePolicy;

        expect($policy->restoreAny($user))->toBeTrue();
    });

    test('restoreAny returns false when user lacks permission', function () {
        $user = User::factory()->create();

        $policy = new RolePolicy;

        expect($policy->restoreAny($user))->toBeFalse();
    });

    test('replicate returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'Replicate:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $role = Role::create(['name' => 'test-role-11', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->replicate($user, $role))->toBeTrue();
    });

    test('replicate returns false when user lacks permission', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'test-role-12', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->replicate($user, $role))->toBeFalse();
    });

    test('reorder returns true when user has permission', function () {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'Reorder:Role', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $policy = new RolePolicy;

        expect($policy->reorder($user))->toBeTrue();
    });

    test('reorder returns false when user lacks permission', function () {
        $user = User::factory()->create();

        $policy = new RolePolicy;

        expect($policy->reorder($user))->toBeFalse();
    });
});

describe('RolePolicy with multiple permissions', function () {

    test('user with multiple permissions can perform multiple actions', function () {
        $user = User::factory()->create();

        $viewAnyPermission = Permission::firstOrCreate(['name' => 'ViewAny:Role', 'guard_name' => 'web']);
        $viewPermission = Permission::firstOrCreate(['name' => 'View:Role', 'guard_name' => 'web']);
        $createPermission = Permission::firstOrCreate(['name' => 'Create:Role', 'guard_name' => 'web']);

        $user->givePermissionTo([$viewAnyPermission, $viewPermission, $createPermission]);

        $role = Role::create(['name' => 'test-role-13', 'guard_name' => 'web']);

        $policy = new RolePolicy;

        expect($policy->viewAny($user))->toBeTrue()
            ->and($policy->view($user, $role))->toBeTrue()
            ->and($policy->create($user))->toBeTrue()
            ->and($policy->update($user, $role))->toBeFalse();
    });
});
