<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionStandingsTest extends TestCase
{
    // use RefreshDatabase;

    private $league;
    private $season;
    private $category;
    private $competition;
    private $teamA;
    private $teamB;
    private $teamC;

    protected function setUp(): void
    {
        parent::setUp();

        $this->league = League::factory()->create();
        $this->season = Season::factory()->create(['league_id' => $this->league->id]);
        $this->category = Category::factory()->create(['league_id' => $this->league->id]);

        $this->competition = Competition::factory()->create([
            'league_id' => $this->league->id,
            'season_id' => $this->season->id,
            'category_id' => $this->category->id,
        ]);

        $this->teamA = Team::factory()->create(['league_id' => $this->league->id, 'name' => 'Lions']);
        $this->teamB = Team::factory()->create(['league_id' => $this->league->id, 'name' => 'Tigers']);
        $this->teamC = Team::factory()->create(['league_id' => $this->league->id, 'name' => 'Bears']);

        $this->competition->teams()->attach([$this->teamA->id, $this->teamB->id, $this->teamC->id]);
    }

    public function test_can_view_competition_standings()
    {
        // Add finished games
        Game::factory()->create([
            'competition_id' => $this->competition->id,
            'category_id' => $this->category->id,
            'home_team_id' => $this->teamA->id,
            'visitor_team_id' => $this->teamB->id,
            'status' => 'finished',
            'home_score' => 5,
            'visitor_score' => 3, // Lions win
            'league_id' => $this->league->id,
        ]);

        Game::factory()->create([
            'competition_id' => $this->competition->id,
            'category_id' => $this->category->id,
            'home_team_id' => $this->teamA->id,
            'visitor_team_id' => $this->teamC->id,
            'status' => 'finished',
            'home_score' => 10,
            'visitor_score' => 2, // Lions win again
            'league_id' => $this->league->id,
        ]);

        Game::factory()->create([
            'competition_id' => $this->competition->id,
            'category_id' => $this->category->id,
            'home_team_id' => $this->teamB->id,
            'visitor_team_id' => $this->teamC->id,
            'status' => 'finished',
            'home_score' => 8,
            'visitor_score' => 4, // Tigers win
            'league_id' => $this->league->id,
        ]);

        $response = $this->get(route('public.competition.show', $this->competition));

        $response->assertStatus(200);
        $response->assertSee($this->competition->name);
        $response->assertSee('Lions');
        $response->assertSee('Tigers');
        $response->assertSee('Bears');
    }

    public function test_can_view_competition_calendar()
    {
        // Add scheduled game
        Game::factory()->create([
            'competition_id' => $this->competition->id,
            'category_id' => $this->category->id,
            'home_team_id' => $this->teamA->id,
            'visitor_team_id' => $this->teamB->id,
            'status' => 'scheduled',
            'start_time' => now()->addDays(2),
            'league_id' => $this->league->id,
        ]);

        $response = $this->get(route('public.competition.calendar', $this->competition));

        $response->assertStatus(200);
        $response->assertSee($this->competition->name);
        $response->assertSee('Lions');
        $response->assertSee('Tigers');
    }
}
