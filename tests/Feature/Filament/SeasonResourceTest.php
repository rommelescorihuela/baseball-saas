<?php

namespace Tests\Feature\Filament;

use App\Models\League;
use App\Models\Season;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeasonResourceTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $league;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->league = League::factory()->create();
        $this->user->leagues()->attach($this->league);
        $this->actingAs($this->user);
    }

    public function test_can_create_season()
    {
        $season = Season::create([
            'name' => 'Winter Season ' . uniqid(),
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
            'league_id' => $this->league->id,
        ]);

        $this->assertDatabaseHas('seasons', [
            'id' => $season->id,
            'name' => $season->name,
            'league_id' => $this->league->id,
        ]);
    }

    public function test_can_edit_season()
    {
        $season = Season::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Season ' . uniqid();

        $season->update(['name' => $newName]);

        $this->assertDatabaseHas('seasons', [
            'id' => $season->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_season()
    {
        $season = Season::factory()->create(['league_id' => $this->league->id]);
        $seasonId = $season->id;

        $season->delete();

        $this->assertDatabaseMissing('seasons', [
            'id' => $seasonId,
        ]);
    }

    public function test_season_belongs_to_league()
    {
        $season = Season::factory()->create(['league_id' => $this->league->id]);

        $this->assertEquals($this->league->id, $season->league_id);
    }
}
