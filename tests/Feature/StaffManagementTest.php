<?php

namespace Tests\Feature;

use App\Filament\App\Resources\TeamResource;
use App\Filament\App\Resources\TeamResource\RelationManagers\UsersRelationManager;
use App\Models\League;
use App\Models\User;
use App\Models\Team;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    public function test_team_owner_can_manage_staff()
    {
        if (!Role::where('name', 'league_owner')->exists()) {
            Role::create(['name' => 'league_owner', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'team_owner')->exists()) {
            Role::create(['name' => 'team_owner', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'coach')->exists()) {
            Role::create(['name' => 'coach', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'secretary')->exists()) {
            Role::create(['name' => 'secretary', 'guard_name' => 'web']);
        }

        // Create League
        $league = League::create([
            'name' => 'Test League ' . uniqid(),
            'slug' => 'test-league-' . uniqid(),
            'status' => 'active',
            'plan' => 'free',
        ]);

        // Create Team Owner
        $teamOwner = User::factory()->create();
        $teamOwner->leagues()->attach($league);
        setPermissionsTeamId($league->id);
        $teamOwner->assignRole('team_owner');

        // Create Team
        $team = Team::create([
            'name' => 'Test Team',
            'league_id' => $league->id,
        ]);
        $team->users()->attach($teamOwner);

        // Authenticate
        $this->actingAs($teamOwner);

        // Set Panel and Tenant context
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($league);

        // Test Relation Manager
        Livewire::test(UsersRelationManager::class, [
            'ownerRecord' => $team,
            'pageClass' => TeamResource\Pages\EditTeam::class,
        ])
            ->assertSuccessful()
            ->assertTableActionExists('create')
            ->callTableAction('create', null, [
                'name' => 'New Staff',
                'email' => 'staff' . uniqid() . '@test.com',
                'password' => 'password',
            ])
            ->assertHasNoErrors();

        // Assert Staff Created and Attached
        $this->assertEquals(2, $team->users()->count()); // Owner + New Staff
    }
}
