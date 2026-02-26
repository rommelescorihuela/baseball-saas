<?php

namespace Tests\Feature\Filament;

use App\Filament\App\Resources\GameResource;
use App\Models\Category;
use App\Models\Game;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

class GameResourceTest extends TestCase
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

    public function test_can_render_game_resource_index_page()
    {
        $this->get(GameResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_create_game()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $competition = \App\Models\Competition::factory()->create(['league_id' => $this->league->id]);
        $homeTeam = Team::factory()->create(['league_id' => $this->league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $this->league->id]);

        $newData = [
            'competition_id' => $competition->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'location' => 'Main Stadium',
            'status' => 'scheduled',
            'home_score' => 0,
            'visitor_score' => 0,
            'league_id' => $this->league->id,
        ];

        Livewire::test(GameResource\Pages\CreateGame::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('games', [
            'location' => 'Main Stadium',
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'league_id' => $this->league->id,
        ]);
    }

    public function test_can_edit_game()
    {
        $game = Game::factory()->create(['league_id' => $this->league->id]);
        $newLocation = 'Updated Stadium ' . uniqid();

        Livewire::test(GameResource\Pages\EditGame::class, [
            'record' => $game->id,
        ])
            ->fillForm([
                'location' => $newLocation,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'location' => $newLocation,
        ]);
    }

    public function test_can_delete_game()
    {
        $game = Game::factory()->create(['league_id' => $this->league->id]);

        Livewire::test(GameResource\Pages\EditGame::class, [
            'record' => $game->id,
        ])
            ->callAction('delete');

        $this->assertDatabaseMissing('games', [
            'id' => $game->id,
        ]);
    }
}
