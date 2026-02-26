<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Models\Season;
use App\Models\Competition;
use App\Models\Category;
use App\Models\Team;
use App\Models\Player;
use App\Models\Game;
use App\Models\PlayerGameStat;
use App\Models\PlayerSeasonStat;
use App\Services\SeasonStatsAggregator;
use App\Services\StandingsCalculator;
use Illuminate\Support\Str;

class TestSaaSEdgeCases extends Command
{
    protected $signature = 'test:saas-edge-cases';
    protected $description = 'Certify Game Edge Cases (Suspended) and Roster Transfers';

    public function handle()
    {
        $this->info("=== Starting Edge Case Certification ===");

        // 1. Setup
        $league = League::create(['name' => 'Edge League', 'slug' => 'edge-' . Str::random(5), 'status' => 'active']);
        $season = Season::create(['league_id' => $league->id, 'name' => 'Season 2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
        $comp = Competition::create(['league_id' => $league->id, 'season_id' => $season->id, 'name' => 'Open Cup', 'status' => 'active']);
        $category = Category::create(['league_id' => $league->id, 'name' => 'Elite']);

        $teamA = Team::create(['league_id' => $league->id, 'name' => 'Team Alpha']);
        $teamB = Team::create(['league_id' => $league->id, 'name' => 'Team Beta']);
        $player = Player::factory()->create(['league_id' => $league->id]);

        $this->info("\n1. Testing Suspended Games Standing Isolation...");

        $gameSuspended = Game::create([
            'league_id' => $league->id,
            'competition_id' => $comp->id,
            'category_id' => $category->id,
            'home_team_id' => $teamA->id,
            'visitor_team_id' => $teamB->id,
            'start_time' => now(),
            'status' => 'suspended',
            'home_score' => 10,
            'visitor_score' => 0
        ]);

        $calculator = new StandingsCalculator($comp);
        $standings = $calculator->calculate();
        $alphaStanding = $standings->where('team_id', $teamA->id)->first();

        if (!$alphaStanding || ($alphaStanding['wins'] == 0)) {
            $this->info("   SUCCESS: Suspended game (10-0) did not grant a win to Team Alpha.");
        } else {
            $this->error("   FAILURE: Team Alpha got a win from a suspended game!");
        }

        $this->info("\n2. Testing Mid-Season Player Transfer Integrity...");

        // Game 1: Player plays for Team A
        $game1 = Game::create([
            'league_id' => $league->id,
            'competition_id' => $comp->id,
            'category_id' => $category->id,
            'home_team_id' => $teamA->id,
            'visitor_team_id' => $teamB->id,
            'start_time' => now()->subDay(),
            'status' => 'finished',
            'home_score' => 1,
            'visitor_score' => 0
        ]);

        PlayerGameStat::create([
            'game_id' => $game1->id,
            'player_id' => $player->id,
            'team_id' => $teamA->id,
            'ab' => 1,
            'h' => 1,
            'hr' => 1
        ]);

        // Game 2: Player plays for Team B (Transfer)
        $game2 = Game::create([
            'league_id' => $league->id,
            'competition_id' => $comp->id,
            'category_id' => $category->id,
            'home_team_id' => $teamB->id,
            'visitor_team_id' => $teamA->id,
            'start_time' => now(),
            'status' => 'finished',
            'home_score' => 1,
            'visitor_score' => 0
        ]);

        PlayerGameStat::create([
            'game_id' => $game2->id,
            'player_id' => $player->id,
            'team_id' => $teamB->id,
            'ab' => 1,
            'h' => 1,
            'hr' => 1
        ]);

        $this->info("   Running Aggregation...");
        $aggregator = new SeasonStatsAggregator();
        $aggregator->aggregateSeason($season);

        $statsA = PlayerSeasonStat::where(['season_id' => $season->id, 'player_id' => $player->id, 'team_id' => $teamA->id])->first();
        $statsB = PlayerSeasonStat::where(['season_id' => $season->id, 'player_id' => $player->id, 'team_id' => $teamB->id])->first();

        if ($statsA && $statsA->hr == 1 && $statsB && $statsB->hr == 1) {
            $this->info("   SUCCESS: Player has separate stats for each team in the same season.");
        } else {
            $this->error("   FAILURE: Transfer stats contaminated or missing.");
            $this->line("   Team A HR: " . ($statsA->hr ?? 'N/A') . " | Team B HR: " . ($statsB->hr ?? 'N/A'));
        }

        // Clean up
        $league->delete();
        $this->info("\n=== Edge Case Certification Complete ===");
    }
}
