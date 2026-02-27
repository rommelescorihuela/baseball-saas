<?php

namespace Tests\Feature\Filament;

use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamResourceTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $league;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'team_owner', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->league = League::factory()->create();
        $this->user->leagues()->attach($this->league);
        $this->actingAs($this->user);
    }

    public function test_can_create_team()
    {
        $team = Team::create([
            'name' => 'Test Team ' . uniqid(),
            'slug' => 'test-team-' . uniqid(),
            'league_id' => $this->league->id,
        ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => $team->name,
            'league_id' => $this->league->id,
        ]);
    }

    public function test_can_edit_team()
    {
        $team = Team::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Team Name ' . uniqid();

        $team->update(['name' => $newName]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_team()
    {
        $team = Team::factory()->create(['league_id' => $this->league->id]);
        $teamId = $team->id;

        $team->delete();

        $this->assertDatabaseMissing('teams', [
            'id' => $teamId,
        ]);
    }

    public function test_team_belongs_to_league_tenant()
    {
        $team = Team::factory()->create(['league_id' => $this->league->id]);

        $this->assertEquals($this->league->id, $team->league_id);
        $this->assertInstanceOf(League::class, $team->league);
    }
}
