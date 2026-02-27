<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use App\Models\League;
use App\Models\Player;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Filament\Facades\Filament;

class SecurityAndEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_team_owner_cannot_access_another_teams_players()
    {
        // 1. Setup two teams and two owners
        $league = League::factory()->create();

        $teamA = Team::factory()->create(['league_id' => $league->id]);
        $ownerA = User::factory()->create();
        $teamA->users()->attach($ownerA->id);

        $teamB = Team::factory()->create(['league_id' => $league->id]);
        $ownerB = User::factory()->create();
        $teamB->users()->attach($ownerB->id);

        $playerB = Player::factory()->create(['team_id' => $teamB->id]);

        // 2. Action: Owner A tries to access Team B context
        $this->actingAs($ownerA);

        // Simulating the filament tenant check
        $this->assertFalse($ownerA->canAccessTenant($teamB));

        // Simulating a request to an academy URL that doesn't belong to them
        $response = $this->get("/academy/{$teamB->slug}/players");

        // Filament returns 404 when tenant is not found in the user's tenants
        $response->assertStatus(404);
    }

    /** @test */
    public function enrollment_requests_are_unique_per_team_category()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        // First attachment
        $category->teams()->attach($team->id, ['status' => 'pending']);

        // Second attachment should fail due to database unique constraint if we bypass UI
        $this->expectException(\Illuminate\Database\QueryException::class);
        $category->teams()->attach($team->id, ['status' => 'pending']);
    }

    /** @test */
    public function public_team_profile_returns_404_for_invalid_slug()
    {
        $response = $this->get('/team/non-existent-slug');
        $response->assertStatus(404);
    }

    /** @test */
    public function a_league_admin_can_only_approve_teams_for_their_own_league_categories()
    {
        // League A
        $leagueA = League::factory()->create();
        $adminA = User::factory()->create();
        $leagueA->users()->attach($adminA->id);
        $categoryA = Category::factory()->create(['league_id' => $leagueA->id]);

        // League B
        $leagueB = League::factory()->create();
        $categoryB = Category::factory()->create(['league_id' => $leagueB->id]);

        $team = Team::factory()->create();
        $categoryB->teams()->attach($team->id, ['status' => 'pending']);

        $this->actingAs($adminA);

        // System should prevent adminA from accessing League B's tenant context
        $this->assertFalse($adminA->canAccessTenant($leagueB));
    }

    /** @test */
    public function a_league_cannot_approve_more_teams_than_its_plan_allows()
    {
        $league = League::factory()->create([
            'plan' => \App\Enums\Plan::FREE, // Limit is 8
        ]);
        $category = Category::factory()->create(['league_id' => $league->id]);

        // Approve 8 teams
        for ($i = 0; $i < 8; $i++) {
            $team = Team::factory()->create();
            $category->teams()->attach($team->id, ['status' => 'approved']);
        }

        $this->assertEquals(8, $league->approvedTeamsCount());
        $this->assertFalse($league->canApproveTeam());

        // Try to approve a 9th team
        $ninthTeam = Team::factory()->create();
        $category->teams()->attach($ninthTeam->id, ['status' => 'pending']);

        // Simulating the action logic found in TeamsRelationManager
        if ($league->canApproveTeam()) {
            $category->teams()->updateExistingPivot($ninthTeam->id, ['status' => 'approved']);
        }

        $this->assertEquals(8, $league->approvedTeamsCount());
        $this->assertDatabaseHas('category_team', [
            'category_id' => $category->id,
            'team_id' => $ninthTeam->id,
            'status' => 'pending'
        ]);
    }
}
