<?php

namespace Tests\Feature\Filament;

use App\Models\Category;
use App\Models\Competition;
use App\Models\League;
use App\Models\Season;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompetitionResourceTest extends TestCase
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

    public function test_can_create_competition()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $season = Season::factory()->create(['league_id' => $this->league->id]);

        $competition = Competition::create([
            'name' => 'Champions Cup ' . uniqid(),
            'category_id' => $category->id,
            'season_id' => $season->id,
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'is_active' => true,
            'league_id' => $this->league->id,
        ]);

        $this->assertDatabaseHas('competitions', [
            'id' => $competition->id,
            'name' => $competition->name,
            'league_id' => $this->league->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_can_edit_competition()
    {
        $competition = Competition::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Cup ' . uniqid();

        $competition->update(['name' => $newName]);

        $this->assertDatabaseHas('competitions', [
            'id' => $competition->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_competition()
    {
        $competition = Competition::factory()->create(['league_id' => $this->league->id]);
        $competitionId = $competition->id;

        $competition->delete();

        $this->assertDatabaseMissing('competitions', [
            'id' => $competitionId,
        ]);
    }

    public function test_competition_belongs_to_league()
    {
        $competition = Competition::factory()->create(['league_id' => $this->league->id]);

        $this->assertEquals($this->league->id, $competition->league_id);
        $this->assertInstanceOf(League::class, $competition->league);
    }
}
