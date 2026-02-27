<?php

namespace Tests\Feature;

use App\Enums\Plan;
use App\Models\Category;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_limit_enforced()
    {
        // 1. Setup League on Free Plan
        $league = League::create([
            'name' => 'Free League ' . uniqid(),
            'slug' => 'free-league-' . uniqid(),
            'status' => 'active',
            'plan' => 'free',
        ]);

        $user = User::factory()->create();
        $user->leagues()->attach($league);
        app(PermissionRegistrar::class)->setPermissionsTeamId($league->id);

        Role::firstOrCreate(['name' => 'league_owner', 'guard_name' => 'web']);
        $user->assignRole('league_owner');

        // 2. Create Max Teams (8) with approved category registrations
        $category = Category::factory()->create(['league_id' => $league->id]);
        $teams = Team::factory()->count(8)->create([
            'league_id' => $league->id,
        ]);
        foreach ($teams as $team) {
            $team->categories()->attach($category->id, ['status' => 'approved']);
        }

        // 3. Verify that canCreateTeam returns false
        $league->refresh();
        $this->assertFalse($league->canCreateTeam());

        // 4. Upgrade Plan
        $league->update(['plan' => 'pro']);

        // 5. Verify canCreateTeam returns true
        $league->refresh();
        $this->assertTrue($league->canCreateTeam());
    }
}
