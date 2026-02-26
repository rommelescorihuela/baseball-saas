<?php

namespace Tests\Unit\Models;

use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    // use RefreshDatabase;

    public function test_user_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id',
                'name',
                'email',
                'password',
            ])
        );
    }

    public function test_user_fillable_attributes()
    {
        $model = new User();
        $expected = [
            'name',
            'email',
            'password'
        ];

        $this->assertEquals($expected, $model->getFillable());
    }

    public function test_user_belongs_to_many_leagues()
    {
        $user = User::factory()->create();
        $league = League::factory()->create();

        $user->leagues()->attach($league);

        $this->assertTrue($user->leagues->contains($league));
        $this->assertInstanceOf(League::class, $user->leagues->first());
    }

    public function test_user_belongs_to_many_teams()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $user->teams()->attach($team);

        $this->assertTrue($user->teams->contains($team));
        $this->assertInstanceOf(Team::class, $user->teams->first());
    }

    public function test_get_tenants_returns_leagues()
    {
        $user = User::factory()->create();
        $league = League::factory()->create();

        $user->leagues()->attach($league);

        $tenants = $user->getTenants(\Filament\Facades\Filament::getPanel('app'));

        $this->assertTrue($tenants->contains($league));
    }

    public function test_can_access_tenant_returns_true_if_user_belongs_to_league()
    {
        $user = User::factory()->create();
        $league = League::factory()->create();

        $user->leagues()->attach($league);

        $this->assertTrue($user->canAccessTenant($league));
    }

    public function test_can_access_tenant_returns_false_if_user_does_not_belong_to_league()
    {
        $user = User::factory()->create();
        $league = League::factory()->create();

        $this->assertFalse($user->canAccessTenant($league));
    }
}
