<?php

namespace Tests\Unit\Policies;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelPolicyTest extends TestCase
{
    // use RefreshDatabase;

    private $userOwner;
    private $userOther;
    private $leagueOwner;
    private $leagueOther;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Users
        $this->userOwner = User::factory()->create();
        $this->userOther = User::factory()->create();

        // Create Leagues (Tenants)
        $this->leagueOwner = League::factory()->create();
        $this->leagueOther = League::factory()->create();

        // Attach Users to Tenants
        $this->userOwner->leagues()->attach($this->leagueOwner);
        $this->userOther->leagues()->attach($this->leagueOther);
    }

    public function test_user_can_access_own_tenant_resources()
    {
        // Category
        $category = Category::factory()->create(['league_id' => $this->leagueOwner->id]);
        $this->assertEquals($this->leagueOwner->id, $category->league_id);

        // Competition
        $competition = Competition::factory()->create(['league_id' => $this->leagueOwner->id]);
        $this->assertEquals($this->leagueOwner->id, $competition->league_id);

        // Season
        $season = Season::factory()->create(['league_id' => $this->leagueOwner->id]);
        $this->assertEquals($this->leagueOwner->id, $season->league_id);

        // Team
        $team = Team::factory()->create(['league_id' => $this->leagueOwner->id]);
        $this->assertEquals($this->leagueOwner->id, $team->league_id);

        // Player
        $player = Player::factory()->create(['league_id' => $this->leagueOwner->id]);
        $this->assertEquals($this->leagueOwner->id, $player->league_id);

        // Game
        $game = Game::factory()->create(['league_id' => $this->leagueOwner->id]);
        $this->assertEquals($this->leagueOwner->id, $game->league_id);
    }

    public function test_tenancy_authorization_logic()
    {
        $this->assertTrue($this->userOwner->canAccessTenant($this->leagueOwner));
        $this->assertFalse($this->userOwner->canAccessTenant($this->leagueOther));

        $this->assertTrue($this->userOther->canAccessTenant($this->leagueOther));
        $this->assertFalse($this->userOther->canAccessTenant($this->leagueOwner));
    }
}
