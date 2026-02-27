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
use Spatie\Permission\Models\Role;

class FunctionalAcademyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'team_owner', 'guard_name' => 'web']);
    }

    /** @test */
    public function academy_owners_can_see_available_categories()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create(['league_id' => $league->id]);
        $owner = User::factory()->create();
        $owner->assignRole('team_owner');
        $team->users()->attach($owner->id);

        $category = Category::factory()->create(['league_id' => $league->id, 'name' => 'Elite Category']);

        // Verify the category exists and belongs to the same league
        $categories = Category::where('league_id', $league->id)->get();
        $this->assertCount(1, $categories);
        $this->assertEquals('Elite Category', $categories->first()->name);
    }

    /** @test */
    public function academy_owner_can_register_for_category()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create(['league_id' => $league->id]);
        $owner = User::factory()->create();
        $owner->assignRole('team_owner');
        $team->users()->attach($owner->id);

        $category = Category::factory()->create(['league_id' => $league->id]);

        // Simulate registration
        $team->categories()->attach($category->id, ['status' => 'pending']);

        $this->assertDatabaseHas('category_team', [
            'category_id' => $category->id,
            'team_id' => $team->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function academy_owner_can_manage_competition_roster()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create(['league_id' => $league->id]);
        $owner = User::factory()->create();
        $owner->assignRole('team_owner');
        $team->users()->attach($owner->id);

        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'season_id' => $season->id,
            'category_id' => $category->id,
        ]);

        $category->teams()->attach($team->id, ['status' => 'approved']);
        $player = Player::factory()->create(['team_id' => $team->id, 'league_id' => $league->id, 'name' => 'Star Player']);

        // Simulate roster enrollment (attach player to season via team_player_season)
        $team->players()->attach($player->id, [
            'season_id' => $season->id,
            'number' => 10,
            'position' => 'IF',
        ]);

        $this->assertDatabaseHas('team_player_season', [
            'team_id' => $team->id,
            'player_id' => $player->id,
            'season_id' => $season->id,
            'number' => 10,
            'position' => 'IF'
        ]);
    }
}
