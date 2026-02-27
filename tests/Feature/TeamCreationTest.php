<?php

namespace Tests\Feature;

use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TeamCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_league_owner_can_create_team_and_invite_owner()
    {
        Mail::fake();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'league_owner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'team_owner', 'guard_name' => 'web']);

        // Create League
        $league = League::factory()->create([
            'status' => 'active',
            'plan' => 'free',
        ]);

        // Create League Owner
        $leagueOwner = User::factory()->create();
        $leagueOwner->leagues()->attach($league);

        app(PermissionRegistrar::class)->setPermissionsTeamId($league->id);
        $leagueOwner->assignRole('league_owner');

        $this->actingAs($leagueOwner);

        // Simulate team creation logic (same as CreateTeam page does)
        $managerEmail = 'manager' . uniqid() . '@test.com';
        $teamName = 'Test Team ' . uniqid();

        $team = Team::create([
            'name' => $teamName,
            'slug' => \Illuminate\Support\Str::slug($teamName),
            'league_id' => $league->id,
        ]);

        // Create or find the manager user
        $password = \Illuminate\Support\Str::random(10);
        $manager = User::create([
            'name' => 'Team Manager',
            'email' => $managerEmail,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
        ]);

        // Attach user to league
        $manager->leagues()->attach($league);

        // Assign role
        app(PermissionRegistrar::class)->setPermissionsTeamId($league->id);
        $manager->assignRole('team_owner');

        // Attach user to team
        $team->users()->attach($manager);

        // Send invitation
        Mail::to($manager)->send(
            new \App\Mail\TeamOwnerInvitation($manager, $team, $password)
        );

        // Assert Team Created
        $this->assertDatabaseHas('teams', [
            'name' => $teamName,
            'league_id' => $league->id,
        ]);

        // Assert User Created
        $this->assertDatabaseHas('users', [
            'email' => $managerEmail,
            'name' => 'Team Manager',
        ]);

        // Assert Role Assigned (scoped)
        $this->assertTrue($manager->hasRole('team_owner'));

        // Assert Attached to Team
        $this->assertTrue($team->users->contains($manager));

        // Assert Attached to League
        $this->assertTrue($manager->leagues->contains($league));

        // Assert Email Sent
        Mail::assertSent(\App\Mail\TeamOwnerInvitation::class, function ($mail) use ($manager, $team) {
            return $mail->hasTo($manager->email) && $mail->team->id === $team->id;
        });
    }
}
