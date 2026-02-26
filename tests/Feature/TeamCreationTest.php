<?php

namespace Tests\Feature;

use App\Filament\App\Resources\TeamResource\Pages\CreateTeam;
use App\Models\League;
use App\Models\User;
use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Mail\TeamOwnerInvitation;

class TeamCreationTest extends TestCase
{
    public function test_league_owner_can_create_team_and_invite_owner()
    {
        Mail::fake();

        // Ensure roles exist
        if (!Role::where('name', 'league_owner')->exists()) {
            Role::create(['name' => 'league_owner', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'team_owner')->exists()) {
            Role::create(['name' => 'team_owner', 'guard_name' => 'web']);
        }

        // Create League
        $league = League::create([
            'name' => 'Test League ' . uniqid(),
            'slug' => 'test-league-' . uniqid(),
            'status' => 'active',
            'plan' => 'free',
        ]);

        // Create League Owner
        $leagueOwner = User::factory()->create();
        $leagueOwner->leagues()->attach($league);
        setPermissionsTeamId($league->id);
        $leagueOwner->assignRole('league_owner');

        // Authenticate as League Owner
        $this->actingAs($leagueOwner);

        // Set Panel and Tenant context
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($league);

        $managerEmail = 'manager' . uniqid() . '@test.com';
        $teamName = 'Test Team ' . uniqid();
        $teamData = [
            'name' => $teamName,
            'owner_name' => 'Team Manager',
            'owner_email' => $managerEmail,
        ];

        Livewire::test(CreateTeam::class)
            ->fillForm($teamData)
            ->call('create')
            ->assertHasNoErrors();

        // Assert Team Created
        $this->assertDatabaseHas('teams', [
            'name' => $teamName,
            'league_id' => $league->id,
        ]);

        $team = Team::where('name', $teamName)->first();

        // Assert User Created
        $this->assertDatabaseHas('users', [
            'email' => $managerEmail,
            'name' => 'Team Manager',
        ]);

        $manager = User::where('email', $managerEmail)->first();

        // Assert Role Assigned (scoped)
        setPermissionsTeamId($league->id); // Ensure we check in correct scope
        $this->assertTrue($manager->hasRole('team_owner'));

        // Assert Attached to Team
        $this->assertTrue($team->users->contains($manager));

        // Assert Attached to League
        $this->assertTrue($manager->leagues->contains($league));

        // Assert Email Sent
        Mail::assertSent(TeamOwnerInvitation::class, function ($mail) use ($manager, $team) {
            return $mail->hasTo($manager->email) && $mail->team->id === $team->id;
        });
    }
}
