<?php

namespace Tests\Feature\Filament;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameResourceTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $league;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->league = League::factory()->create();
        $this->user->leagues()->attach($this->league);
        $this->actingAs($this->user);
    }

    public function test_can_create_game()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $season = Season::factory()->create(['league_id' => $this->league->id]);
        $competition = Competition::factory()->create([
            'season_id' => $season->id,
            'category_id' => $category->id,
        ]);
        $homeTeam = Team::factory()->create(['league_id' => $this->league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $this->league->id]);

        $game = Game::create([
            'competition_id' => $competition->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'start_time' => now()->addDays(1),
            'location' => 'Main Stadium',
            'status' => 'scheduled',
            'home_score' => 0,
            'visitor_score' => 0,
            'league_id' => $this->league->id,
        ]);

        $this->assertDatabaseHas('games', [
            'id' => $game->id,
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

        $game->update(['location' => $newLocation]);

        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'location' => $newLocation,
        ]);
    }

    public function test_can_delete_game()
    {
        $game = Game::factory()->create(['league_id' => $this->league->id]);
        $gameId = $game->id;

        $game->delete();

        $this->assertDatabaseMissing('games', [
            'id' => $gameId,
        ]);
    }

    public function test_game_belongs_to_league_tenant()
    {
        $game = Game::factory()->create(['league_id' => $this->league->id]);

        $this->assertEquals($this->league->id, $game->league_id);
        $this->assertInstanceOf(League::class, $game->league);
    }
}
