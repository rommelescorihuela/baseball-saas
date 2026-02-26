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
use Illuminate\Support\Str;

class TestSaaSStats extends Command
{
    protected $signature = 'test:saas-stats';
    protected $description = 'Certify Statistical Aggregation Integrity';

    public function handle()
    {
        $this->info("=== Starting Statistical Integrity Certification ===");

        // 1. Setup
        $league = League::create(['name' => 'Stats League', 'slug' => 'stats-' . Str::random(5), 'status' => 'active']);
        $season2026 = Season::create([
            'league_id' => $league->id,
            'name' => 'Season 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31'
        ]);
        $comp = Competition::create([
            'league_id' => $league->id,
            'season_id' => $season2026->id,
            'name' => 'Championship',
            'status' => 'active'
        ]);
        $category = Category::create(['league_id' => $league->id, 'name' => 'Pro']);
        $team = Team::create(['league_id' => $league->id, 'name' => 'The Avengers']);
        $visitor = Team::create(['league_id' => $league->id, 'name' => 'The Villains']);
        $player = Player::factory()->create(['league_id' => $league->id]);

        $this->info("1. Simulating 5 Games with 1 HR each for Player ID: {$player->id}");

        for ($i = 0; $i < 5; $i++) {
            $game = Game::create([
                'league_id' => $league->id,
                'competition_id' => $comp->id,
                'category_id' => $category->id,
                'home_team_id' => $team->id,
                'visitor_team_id' => $visitor->id,
                'start_time' => now(),
                'status' => 'finished',
                'home_score' => 1,
                'visitor_score' => 0
            ]);

            PlayerGameStat::create([
                'game_id' => $game->id,
                'player_id' => $player->id,
                'team_id' => $team->id,
                'ab' => 4,
                'h' => 1,
                'hr' => 1,
                'r' => 1,
                'rbi' => 1
            ]);
        }

        // 2. Run Aggregation
        $this->info("\n2. Running SeasonStatsAggregator for 2026...");
        $aggregator = new SeasonStatsAggregator();
        $aggregator->aggregateSeason($season2026);

        $seasonStat = PlayerSeasonStat::where([
            'season_id' => $season2026->id,
            'player_id' => $player->id,
            'team_id' => $team->id
        ])->first();

        if ($seasonStat && $seasonStat->hr == 5) {
            $this->info("   SUCCESS: Player accumulated exactly 5 HR in Season 2026.");
        } else {
            $this->error("   FAILURE: Expected 5 HR, got " . ($seasonStat->hr ?? 0));
        }

        // 3. Multi-Season Test (Aisolation)
        $this->info("\n3. Testing Season Isolation (2027)...");
        $season2027 = Season::create([
            'league_id' => $league->id,
            'name' => 'Season 2027',
            'start_date' => '2027-01-01',
            'end_date' => '2027-12-31'
        ]);
        $comp2027 = Competition::create([
            'league_id' => $league->id,
            'season_id' => $season2027->id,
            'name' => 'Championship 2027',
            'status' => 'active'
        ]);

        $game2027 = Game::create([
            'league_id' => $league->id,
            'competition_id' => $comp2027->id,
            'category_id' => $category->id,
            'home_team_id' => $team->id,
            'visitor_team_id' => $visitor->id,
            'start_time' => now()->addYear(),
            'status' => 'finished',
            'home_score' => 1,
            'visitor_score' => 0
        ]);

        PlayerGameStat::create([
            'game_id' => $game2027->id,
            'player_id' => $player->id,
            'team_id' => $team->id,
            'ab' => 4,
            'h' => 2,
            'hr' => 2,
            'r' => 2,
            'rbi' => 2
        ]);

        $this->info("   Running Aggregator only for 2027...");
        $aggregator->aggregateSeason($season2027);

        $stat2027 = PlayerSeasonStat::where([
            'season_id' => $season2027->id,
            'player_id' => $player->id,
            'team_id' => $team->id
        ])->first();

        if ($stat2027 && $stat2027->hr == 2) {
            $this->info("   SUCCESS: Season 2027 has its own stats (2 HR).");
        } else {
            $this->error("   FAILURE: 2027 stats corrupted. Got " . ($stat2027->hr ?? 0));
        }

        $seasonStat->refresh();
        if ($seasonStat->hr == 5) {
            $this->info("   SUCCESS: Season 2026 remains untouched (Exactly 5 HR).");
        } else {
            $this->error("   FAILURE: Season 2026 was contaminated! Got " . $seasonStat->hr);
        }

        // Clean up
        $league->delete();
        $this->info("\n=== Statistical Integrity Certification Complete ===");
    }
}
