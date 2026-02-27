<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\League;
use App\Models\Category;
use App\Models\Season;
use App\Enums\Plan;
use App\Filament\App\Resources\CategoryResource;
use App\Filament\App\Resources\CompetitionResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;

class SubscriptionFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'league_owner', 'guard_name' => 'web']);
        Filament::setCurrentPanel(Filament::getPanel('app'));
    }

    /** @test */
    public function free_plan_league_cannot_exceed_category_limit()
    {
        $league = League::factory()->create(['plan' => Plan::FREE]);
        $owner = User::factory()->create();
        $owner->assignRole('league_owner');
        $league->users()->attach($owner->id);

        // Create 1 category (limit is 1 for FREE)
        Category::factory()->create(['league_id' => $league->id]);

        $this->actingAs($owner);
        Filament::setTenant($league);

        Livewire::withQueryParams(['tenant' => $league->slug])
            ->test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'name' => 'Second Category',
            ])
            ->call('create')
            ->assertNotified('Límite de categorías alcanzado');

        $this->assertEquals(1, Category::where('league_id', $league->id)->count());
    }

    /** @test */
    public function free_plan_league_cannot_exceed_competition_limit()
    {
        $league = League::factory()->create(['plan' => Plan::FREE]);
        $owner = User::factory()->create();
        $owner->assignRole('league_owner');
        $league->users()->attach($owner->id);

        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);

        // Create 1 competition (limit is 1 for FREE)
        \App\Models\Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $this->actingAs($owner);
        Filament::setTenant($league);

        Livewire::withQueryParams(['tenant' => $league->slug])
            ->test(CompetitionResource\Pages\CreateCompetition::class)
            ->fillForm([
                'name' => 'Second Competition',
                'season_id' => $season->id,
                'category_id' => $category->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
            ])
            ->call('create')
            ->assertNotified('Límite de competencias alcanzado');

        $this->assertEquals(1, \App\Models\Competition::whereHas('category', fn($q) => $q->where('league_id', $league->id))->count());
    }
}
