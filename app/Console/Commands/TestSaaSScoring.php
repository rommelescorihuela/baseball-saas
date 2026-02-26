<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;
use App\Models\Team;
use App\Models\Competition;
use App\Models\Season;
use App\Models\League;
use App\Models\Player;
use App\Models\GameEvent;
use Illuminate\Support\Str;

class TestSaaSScoring extends Command
{
    protected $signature = 'test:saas-scoring';
    protected $description = 'Simulate Live Scoring and verify Standings propagation';

    public function handle()
    {
        $this->info("=== Starting Live Scoring Certification ===");

        // 1. Setup Environment
        $league = League::create(['name' => 'Demo League', 'slug' => 'demo-' . Str::random(5), 'status' => 'active']);
        $season = Season::create([
            'league_id' => $league->id,
            'name' => '2026 Season',
            'start_date' => now(),
            'end_date' => now()->addMonths(6)
        ]);

        $comp = Competition::create([
            'league_id' => $league->id,
            'season_id' => $season->id,
            'name' => 'World Series',
            'status' => 'active'
        ]);

        $category = \App\Models\Category::create(['league_id' => $league->id, 'name' => 'Senior']);

        $teamHome = Team::create(['league_id' => $league->id, 'name' => 'Home Giants']);
        $teamVisitor = Team::create(['league_id' => $league->id, 'name' => 'Visitor Sharks']);

        // Mandatory players for events
        $batter = Player::factory()->create(['league_id' => $league->id]);
        $pitcher = Player::factory()->create(['league_id' => $league->id]);

        $game = Game::create([
            'league_id' => $league->id,
            'competition_id' => $comp->id,
            'season_id' => $season->id,
            'category_id' => $category->id,
            'home_team_id' => $teamHome->id,
            'visitor_team_id' => $teamVisitor->id,
            'status' => 'scheduled',
            'start_time' => now(),
        ]);

        $this->info("1. Game Created: Giant vs Sharks. Initial Status: " . $game->status);

        // 2. Simulate In-Game Events
        $this->info("\n2. Simulating Home Run for Home Giants...");

        GameEvent::create([
            'game_id' => $game->id,
            'inning' => 1,
            'is_top_inning' => false, // Bottom 1st
            'team_id' => $teamHome->id,
            'batter_id' => $batter->id,
            'pitcher_id' => $pitcher->id,
            'type' => 'hit',
            'result' => json_encode(['kind' => 'HR']),
            'runs_scored' => 1,
        ]);

        // We must update the game scores since calculator reads from columns
        $game->update([
            'home_score' => 1,
            'visitor_score' => 0,
            'status' => 'finished'
        ]);

        $this->info("   Game Finished. Final Score: Giants 1 - Sharks 0");

        // 3. Check Standings propagation
        $this->info("\n3. Verifying Standings Propagation...");

        $calculator = new \App\Services\StandingsCalculator($comp);
        $standings = $calculator->calculate();

        $giantStanding = $standings->where('team_id', $teamHome->id)->first();

        if ($giantStanding && $giantStanding['wins'] == 1) {
            $this->info("   SUCCESS: Giants now have 1 WIN in the standings (Service Verified).");
        } else {
            $this->error("   FAILURE: Standings did not reflect win.");
            $this->logStanding($giantStanding);
        }

        // Clean up
        $league->delete();
        $this->info("\n=== Live Scoring Certification Complete ===");
    }

    protected function logStanding($s)
    {
        if (!$s) {
            $this->error("      No standing found for Home Giants.");
            return;
        }
        $this->line("      Giants Standing - Wins: " . ($s['wins'] ?? 0) . " | Losses: " . ($s['losses'] ?? 0));
    }
}
