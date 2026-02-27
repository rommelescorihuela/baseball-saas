<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\League;
use App\Filament\Resources\Leagues\Pages\ListLeagues;
use App\Filament\Resources\Leagues\Pages\CreateLeague;
use App\Filament\Resources\Leagues\Pages\EditLeague;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use Filament\Facades\Filament;

class SuperAdminFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and initial data
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        // Ensure we are in the GLOBAL admin panel context
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /** @test */
    public function super_admin_can_list_all_leagues()
    {
        $superAdmin = User::where('email', 'admin@baseball-saas.com')->first();

        $leagues = League::where('slug', '!=', 'admin')->get(); // Seeded leagues

        $this->actingAs($superAdmin);

        Livewire::test(ListLeagues::class)
            ->assertCanSeeTableRecords($leagues->take(5))
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('slug')
            ->assertTableColumnExists('plan');
    }

    /** @test */
    public function super_admin_can_create_a_new_league()
    {
        $superAdmin = User::where('email', 'admin@baseball-saas.com')->first();

        $this->actingAs($superAdmin);

        Livewire::test(CreateLeague::class)
            ->fillForm([
                'name' => 'Major League Test',
                'slug' => 'major-league-test',
                'plan' => 'pro',
                'owner_name' => 'Test Owner',
                'owner_email' => 'owner-test@league.com',
                'owner_password' => 'password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('leagues', [
            'name' => 'Major League Test',
            'slug' => 'major-league-test',
        ]);
    }

    /** @test */
    public function super_admin_can_edit_league_plan()
    {
        $superAdmin = User::where('email', 'admin@baseball-saas.com')->first();

        $league = League::factory()->create(['plan' => 'free']);

        $this->actingAs($superAdmin);

        Livewire::test(EditLeague::class, [
            'record' => $league->getRouteKey(),
        ])
            ->fillForm([
                'plan' => 'unlimited',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('unlimited', $league->refresh()->plan->value);
    }
}
