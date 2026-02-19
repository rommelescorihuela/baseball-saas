<?php

namespace Tests\Feature;

use App\Enums\Plan;
use App\Filament\App\Pages\Subscription;
use App\Models\League;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubscriptionPageTest extends TestCase
{
    public function test_league_owner_can_access_subscription_page()
    {
        // Ensure roles exist
        if (!Role::where('name', 'league_owner')->exists()) {
             Role::create(['name' => 'league_owner', 'guard_name' => 'web']);
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

        // Test Page Access
        Livewire::test(Subscription::class)
            ->assertSuccessful()
            ->assertViewHas('plans')
            ->assertSee('Plan Gratuito');
    }

    public function test_non_owner_cannot_access_subscription_page()
    {
        // Create League
        $league = League::create([
            'name' => 'Test League 2 ' . uniqid(),
            'slug' => 'test-league-2-' . uniqid(),
            'status' => 'active',
            'plan' => 'free',
        ]);

        // Create Regular User (Team Owner or just user)
        $user = User::factory()->create();
        $user->leagues()->attach($league);
        // Do not assign league_owner role

        // Authenticate
        $this->actingAs($user);

        // Set Panel and Tenant context
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($league);
        
        // Test Page Access - Should fail authorization or return 403
        Livewire::test(Subscription::class)
            ->assertForbidden();
    }
}
