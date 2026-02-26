<?php

namespace Tests\Feature\Filament;

use App\Filament\App\Resources\SeasonResource;
use App\Models\League;
use App\Models\Season;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

class SeasonResourceTest extends TestCase
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

    public function test_can_render_season_resource_index_page()
    {
        $this->get(SeasonResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_create_season()
    {
        $newData = [
            'name' => 'Winter Season ' . uniqid(),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'is_active' => true,
            'league_id' => $this->league->id,
        ];

        Livewire::test(SeasonResource\Pages\CreateSeason::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('seasons', [
            'name' => $newData['name'],
            'league_id' => $this->league->id,
        ]);
    }

    public function test_can_edit_season()
    {
        $season = Season::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Season Name ' . uniqid();

        Livewire::test(SeasonResource\Pages\EditSeason::class, [
            'record' => $season->id,
        ])
            ->fillForm([
                'name' => $newName,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('seasons', [
            'id' => $season->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_season()
    {
        $season = Season::factory()->create(['league_id' => $this->league->id]);

        Livewire::test(SeasonResource\Pages\EditSeason::class, [
            'record' => $season->id,
        ])
            ->callAction('delete');

        $this->assertDatabaseMissing('seasons', [
            'id' => $season->id,
        ]);
    }
}
