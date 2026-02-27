<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\League;
use App\Models\Category;
use App\Models\Season;
use App\Models\Competition;
use App\Filament\App\Resources\CategoryResource;
use App\Filament\App\Resources\SeasonResource;
use App\Filament\App\Resources\CompetitionResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;

class LeagueAdminFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Roles manually since DatabaseSeeder might be too heavy/conflicting for this specific UI test
        Role::firstOrCreate(['name' => 'league_owner', 'guard_name' => 'web']);

        Filament::setCurrentPanel(Filament::getPanel('app'));
    }

    /** @test */
    public function league_admin_can_list_their_categories()
    {
        $league = League::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('league_owner');
        $admin->leagues()->attach($league->id);

        $categories = Category::factory()->count(2)->create(['league_id' => $league->id]);

        $this->actingAs($admin);
        Filament::setTenant($league);

        Livewire::withQueryParams(['tenant' => $league->slug])
            ->test(CategoryResource\Pages\ListCategories::class)
            ->assertTableColumnExists('name')
            ->assertCanSeeTableRecords($categories);
    }

    /** @test */
    public function league_admin_can_create_a_season()
    {
        $league = League::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('league_owner');
        $admin->leagues()->attach($league->id);

        $this->actingAs($admin);
        Filament::setTenant($league);

        Livewire::withQueryParams(['tenant' => $league->slug])
            ->test(SeasonResource\Pages\CreateSeason::class)
            ->fillForm([
                'name' => 'Season 2027',
                'is_active' => true,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(6)->format('Y-m-d'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('seasons', [
            'league_id' => $league->id,
            'name' => 'Season 2027',
        ]);
    }

    /** @test */
    public function league_admin_can_create_a_competition()
    {
        $league = League::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('league_owner');
        $admin->leagues()->attach($league->id);

        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);

        $this->actingAs($admin);
        Filament::setTenant($league);

        Livewire::withQueryParams(['tenant' => $league->slug])
            ->test(CompetitionResource\Pages\CreateCompetition::class)
            ->fillForm([
                'name' => 'Torneo Verano',
                'season_id' => $season->id,
                'category_id' => $category->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('competitions', [
            'league_id' => $league->id,
            'name' => 'Torneo Verano',
        ]);
    }
}
