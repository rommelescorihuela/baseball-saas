<?php

namespace Tests\Feature;

use App\Enums\Plan;
use App\Filament\App\Resources\TeamResource\Pages\CreateTeam;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamLimitTest extends TestCase
{
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
        setPermissionsTeamId($league->id);
        
        if (!Role::where('name', 'league_owner')->exists()) {
             Role::create(['name' => 'league_owner', 'guard_name' => 'web']);
        }
        $user->assignRole('league_owner');

        // 2. Create Max Teams (8)
        Team::factory()->count(8)->create([
            'league_id' => $league->id,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($league);

        // 3. Attempt to visit Create Page -> Should Redirect
        Livewire::test(CreateTeam::class)
            ->assertRedirect(); // Should redirect to index
            
        // 4. Upgrade Plan
        $league->update(['plan' => 'pro']);
        
        // 5. Attempt to visit Create Page -> Should Succeed
        Livewire::test(CreateTeam::class)
            ->assertSuccessful();
    }
}
