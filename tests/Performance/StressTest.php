<?php

namespace Tests\Performance;

use App\Models\User;
use App\Models\Team;
use App\Models\League;
use App\Models\Player;
use App\Models\Category;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_handles_massive_player_loading_and_roster_selection()
    {
        // 1. Setup: 1 League, 1 Team, and 1,000 Players
        $league = League::factory()->create(['plan' => \App\Enums\Plan::UNLIMITED]);
        $team = Team::factory()->create(['league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'season_id' => $season->id,
            'category_id' => $category->id,
            'league_id' => $league->id
        ]);

        echo "\nGenerando 10,000 jugadores...";
        $start = microtime(true);
        Player::factory()->count(10000)->create(['team_id' => $team->id, 'league_id' => $league->id]);
        $end = microtime(true);
        echo "\nCreación de 10,000 jugadores: " . round($end - $start, 2) . "s";

        // 2. Attach 2,000 players to the competition roster
        echo "\nInscribiendo 2,000 jugadores en el roster...";
        $players = Player::where('team_id', $team->id)->take(2000)->get();
        $pivotData = $players->map(fn($p) => [
            'team_id' => $team->id,
            'player_id' => $p->id,
            'season_id' => $season->id,
            'number' => rand(1, 99),
            'position' => 'P',
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        $start = microtime(true);
        foreach (array_chunk($pivotData, 500) as $chunk) {
            DB::table('team_player_season')->insert($chunk);
        }
        $end = microtime(true);
        echo "\nInserción masiva de roster (2,000): " . round($end - $start, 2) . "s";

        // 3. Test Query Performance
        \Filament\Facades\Filament::shouldReceive('getTenant')->andReturn($team);

        $start = microtime(true);
        $roster = $competition->roster_players;
        $end = microtime(true);

        $queryTime = round($end - $start, 4);
        echo "\nConsulta de Roster (2,000 jugadores): " . $queryTime . "s";

        $this->assertCount(2000, $roster);
        $this->assertLessThan(0.1, $queryTime, "La consulta de roster es demasiado lenta (> 100ms)");
    }

    /** @test */
    public function it_handles_massive_enrollment_requests_validation()
    {
        $league = League::factory()->create(['plan' => \App\Enums\Plan::UNLIMITED]);
        $category = Category::factory()->create(['league_id' => $league->id]);

        echo "\nGenerando 200 equipos y enviando solicitudes de inscripción...";
        $teams = Team::factory()->count(200)->create(['league_id' => null]); // Academies

        $pivotData = $teams->map(fn($t) => [
            'category_id' => $category->id,
            'team_id' => $t->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        $start = microtime(true);
        DB::table('category_team')->insert($pivotData);
        $end = microtime(true);
        echo "\nEnvío de 200 solicitudes: " . round($end - $start, 2) . "s";

        // Test global approval counting performance
        $start = microtime(true);
        $count = $league->approvedTeamsCount();
        $end = microtime(true);

        echo "\nConteo de equipos aprobados: " . round($end - $start, 4) . "s";
        $this->assertEquals(0, $count);
    }
}
