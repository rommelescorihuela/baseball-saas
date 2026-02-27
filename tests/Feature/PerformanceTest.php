<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\League;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function team_profile_page_loads_under_200ms()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create(['league_id' => $league->id]);

        // Populate with players to simulate real load
        Player::factory()->count(15)->create(['team_id' => $team->id, 'league_id' => $league->id]);

        $start = microtime(true);
        $response = $this->get(route('public.team.show', $team));
        $end = microtime(true);

        $duration = ($end - $start) * 1000; // in ms

        $response->assertStatus(200);

        $this->assertLessThan(200, $duration, "Team profile page took {$duration}ms to load, which is over the 200ms limit.");

        dump("Team Profile Load Time: {$duration}ms");
    }
}
