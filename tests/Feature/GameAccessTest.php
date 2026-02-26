<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameAccessTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'league_owner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'coach', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'scorer', 'guard_name' => 'web']);
    }

    public function test_league_owner_can_access_any_game()
    {
        $league = League::create(['name' => 'L1', 'slug' => 'l1-' . \Illuminate\Support\Str::random(5), 'status' => 'active']);
        $owner = User::factory()->create();
        $owner->leagues()->attach($league);
        $owner->assignRole('league_owner');

        $game = Game::factory()->create(['league_id' => $league->id]);

        $this->actingAs($owner);
        $this->assertTrue($owner->can('update', $game));
    }

    public function test_scorer_can_access_any_game_in_league()
    {
        $league = League::create(['name' => 'L1', 'slug' => 'l1-' . \Illuminate\Support\Str::random(5), 'status' => 'active']);
        $scorer = User::factory()->create();
        $scorer->leagues()->attach($league);
        $scorer->assignRole('scorer');

        $game = Game::factory()->create(['league_id' => $league->id]);

        $this->actingAs($scorer);
        $this->assertTrue($scorer->can('update', $game));
    }

    public function test_coach_can_only_access_their_own_games()
    {
        $league = League::create(['name' => 'L1', 'slug' => 'l1-' . \Illuminate\Support\Str::random(5), 'status' => 'active']);
        $coach = User::factory()->create();
        $coach->leagues()->attach($league);
        $coach->assignRole('coach');

        $myTeam = Team::create(['league_id' => $league->id, 'name' => 'My Team']);
        $otherTeam = Team::create(['league_id' => $league->id, 'name' => 'Other Team']);
        $otherTeam2 = Team::create(['league_id' => $league->id, 'name' => 'Other Team 2']);

        $coach->teams()->attach($myTeam);

        // Game where I am home
        $gameHome = Game::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $myTeam->id,
            'visitor_team_id' => $otherTeam->id
        ]);

        // Game where I am visitor
        $gameVisitor = Game::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $otherTeam->id,
            'visitor_team_id' => $myTeam->id
        ]);

        // Game where I am not involved
        $gameNotMine = Game::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $otherTeam->id,
            'visitor_team_id' => $otherTeam2->id
        ]);

        $this->actingAs($coach);

        $this->assertTrue($coach->can('update', $gameHome), 'Coach should access home game');
        $this->assertTrue($coach->can('update', $gameVisitor), 'Coach should access visitor game');
        $this->assertFalse($coach->can('update', $gameNotMine), 'Coach should NOT access game where they dont play');
    }
}
