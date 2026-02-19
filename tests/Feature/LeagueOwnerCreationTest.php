<?php

namespace Tests\Feature;

use App\Filament\Resources\Leagues\Pages\CreateLeague;
use App\Models\User;
use App\Models\League;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Mail\LeagueOwnerCreated;

class LeagueOwnerCreationTest extends TestCase
{
    // use RefreshDatabase; // Use with caution on existing DB. Maybe DatabaseTruncation if available or manual cleanup.
    // Given the environment, I'll rely on manual cleanup or specific distinct data.

    public function test_league_creation_creates_owner_and_sends_email()
    {
        Mail::fake();

        // Ensure roles exist
        if (!Role::where('name', 'super_admin')->exists()) {
             Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'league_owner')->exists()) {
             Role::create(['name' => 'league_owner', 'guard_name' => 'web']);
        }

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $leagueData = [
            'name' => 'Test League ' . uniqid(),
            'slug' => 'test-league-' . uniqid(),
            'status' => 'active',
            'plan' => 'free',
            'owner_name' => 'Test Owner',
            'owner_email' => 'owner' . uniqid() . '@example.com',
            'owner_password' => 'password123',
        ];

        Livewire::actingAs($superAdmin)
            ->test(CreateLeague::class)
            ->fillForm($leagueData)
            ->call('create')
            ->assertHasNoErrors();

        // Assert League Created
        $this->assertDatabaseHas('leagues', [
            'name' => $leagueData['name'],
            'slug' => $leagueData['slug'],
        ]);

        // Assert User Created
        $this->assertDatabaseHas('users', [
            'email' => $leagueData['owner_email'],
            'name' => 'Test Owner',
        ]);

        $owner = User::where('email', $leagueData['owner_email'])->first();
        $this->assertTrue($owner->hasRole('league_owner'));

        // Assert Pivot
        $league = League::where('slug', $leagueData['slug'])->first();
        $this->assertTrue($league->users->contains($owner));

        // Assert Email Sent
        Mail::assertSent(LeagueOwnerCreated::class, function ($mail) use ($owner) {
            return $mail->hasTo($owner->email);
        });
    }
}
