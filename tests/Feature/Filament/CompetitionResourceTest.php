<?php

namespace Tests\Feature\Filament;

use App\Filament\App\Resources\CompetitionResource;
use App\Models\Category;
use App\Models\Competition;
use App\Models\League;
use App\Models\Season;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

class CompetitionResourceTest extends TestCase
{
    private $user;
    private $league;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->league = League::factory()->create();
        $this->user->leagues()->attach($this->league);

        $this->actingAs($this->user);

        // Filament Tenancy Context
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->league);
    }

    public function test_can_render_competition_resource_index_page()
    {
        $this->get(CompetitionResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_create_competition()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $season = Season::factory()->create(['league_id' => $this->league->id]);

        $newData = [
            'name' => 'Champions Cup ' . uniqid(),
            'category_id' => $category->id,
            'season_id' => $season->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(3)->format('Y-m-d'),
            'is_active' => true,
            'league_id' => $this->league->id,
        ];

        Livewire::test(CompetitionResource\Pages\CreateCompetition::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('competitions', [
            'name' => $newData['name'],
            'league_id' => $this->league->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_can_edit_competition()
    {
        $competition = Competition::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Cup Name ' . uniqid();

        Livewire::test(CompetitionResource\Pages\EditCompetition::class, [
            'record' => $competition->id,
        ])
            ->fillForm([
                'name' => $newName,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('competitions', [
            'id' => $competition->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_competition()
    {
        $competition = Competition::factory()->create(['league_id' => $this->league->id]);

        Livewire::test(CompetitionResource\Pages\EditCompetition::class, [
            'record' => $competition->id,
        ])
            ->callAction('delete');

        $this->assertDatabaseMissing('competitions', [
            'id' => $competition->id,
        ]);
    }
}
