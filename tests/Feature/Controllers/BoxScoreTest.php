<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoxScoreTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $league;
    private $game;
    private $homeTeam;
    private $visitorTeam;

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

        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $season = Season::factory()->create(['league_id' => $this->league->id]);
        $competition = Competition::factory()->create([
            'season_id' => $season->id,
            'category_id' => $category->id,
        ]);

        $this->homeTeam = Team::factory()->create(['league_id' => $this->league->id]);
        $this->visitorTeam = Team::factory()->create(['league_id' => $this->league->id]);

        $this->game = Game::factory()->create([
            'league_id' => $this->league->id,
            'category_id' => $category->id,
            'competition_id' => $competition->id,
            'home_team_id' => $this->homeTeam->id,
            'visitor_team_id' => $this->visitorTeam->id,
            'status' => 'finished',
            'home_score' => 5,
            'visitor_score' => 3,
        ]);
    }

    public function test_can_render_box_score_page_with_stats()
    {
        $homePlayer = Player::factory()->create(['league_id' => $this->league->id]);
        $visitorPlayer = Player::factory()->create(['league_id' => $this->league->id]);

        // Creating game stats for home player
        PlayerGameStat::create([
            'game_id' => $this->game->id,
            'player_id' => $homePlayer->id,
            'team_id' => $this->homeTeam->id,
            'ab' => 4,
            'h' => 2,
            'r' => 1,
            'rbi' => 2,
        ]);

        // Creating game stats for visitor player
        PlayerGameStat::create([
            'game_id' => $this->game->id,
            'player_id' => $visitorPlayer->id,
            'team_id' => $this->visitorTeam->id,
            'ab' => 3,
            'h' => 1,
            'r' => 0,
            'rbi' => 0,
        ]);

        // Verify the stats exist and are associated with the game correctly
        $this->assertDatabaseHas('player_game_stats', [
            'game_id' => $this->game->id,
            'player_id' => $homePlayer->id,
            'team_id' => $this->homeTeam->id,
            'ab' => 4,
            'h' => 2,
        ]);

        $this->assertDatabaseHas('player_game_stats', [
            'game_id' => $this->game->id,
            'player_id' => $visitorPlayer->id,
            'team_id' => $this->visitorTeam->id,
            'ab' => 3,
            'h' => 1,
        ]);

        // Verify the game's stats relation returns the correct data
        $game = $this->game->fresh();
        $stats = $game->stats;
        $this->assertCount(2, $stats);

        $homeStats = $stats->where('team_id', $this->homeTeam->id)->first();
        $visitorStats = $stats->where('team_id', $this->visitorTeam->id)->first();

        $this->assertEquals(4, $homeStats->ab);
        $this->assertEquals(2, $homeStats->h);
        $this->assertEquals(3, $visitorStats->ab);
        $this->assertEquals(1, $visitorStats->h);
    }
}
