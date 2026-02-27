<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use App\Models\League;
use App\Models\Category;
use App\Models\Season;
use App\Models\Competition;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Filament\Facades\Filament;

class EnrollmentFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_academy_owner_can_request_enrollment_in_a_category()
    {
        // 1. Setup: Create Academy and Owner
        $league = League::factory()->create();
        $team = Team::factory()->create(['league_id' => null]); // Independent Academy
        $owner = User::factory()->create();
        $team->users()->attach($owner->id);

        // 2. Setup: Create another League with a Category
        $externalLeague = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $externalLeague->id]);

        // 3. Action: Attach team to category with pending status (simulating the 'register' action)
        $category->teams()->attach($team->id, ['status' => 'pending']);

        // 4. Assert: Database has the pending enrollment
        $this->assertDatabaseHas('category_team', [
            'category_id' => $category->id,
            'team_id' => $team->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function a_league_admin_can_approve_an_enrollment_request()
    {
        // 1. Setup
        $league = League::factory()->create();
        $team = Team::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        // Initial pending state
        $category->teams()->attach($team->id, ['status' => 'pending']);

        // 2. Action: Approve (simulating the 'approve' action in the relation manager)
        $pivot = $category->teams()->where('team_id', $team->id)->first()->pivot;
        $pivot->status = 'approved';
        $pivot->save();

        // 3. Assert
        $this->assertDatabaseHas('category_team', [
            'category_id' => $category->id,
            'team_id' => $team->id,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function an_approved_academy_can_manage_roster_for_a_competition()
    {
        // 1. Setup
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'season_id' => $season->id,
            'category_id' => $category->id,
            'league_id' => $league->id
        ]);

        $team = Team::factory()->create();
        $category->teams()->attach($team->id, ['status' => 'approved']);

        $player = Player::factory()->create(['team_id' => $team->id]);

        // 2. Action: Add player to roster for this season/competition
        // We use the same table team_player_season
        $team->players()->attach($player->id, [
            'season_id' => $season->id,
            'number' => 42,
            'position' => 'P'
        ]);

        // 3. Assert
        $this->assertDatabaseHas('team_player_season', [
            'team_id' => $team->id,
            'player_id' => $player->id,
            'season_id' => $season->id,
            'number' => 42
        ]);
    }
}
