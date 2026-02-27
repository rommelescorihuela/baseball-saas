<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_competition_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('competitions', [
                'id',
                'season_id',
                'category_id',
                'league_id',
                'name',
                'start_date',
                'end_date',
                'status',
                'is_active',
                'created_at',
                'updated_at',
            ])
        );
    }

    public function test_competition_fillable_attributes()
    {
        $model = new Competition();
        $expected = [
            'season_id',
            'category_id',
            'league_id',
            'name',
            'start_date',
            'end_date',
            'status',
            'is_active',
        ];

        $this->assertEquals($expected, $model->getFillable());
    }

    public function test_competition_has_casts()
    {
        $model = new Competition();

        $this->assertArrayHasKey('start_date', $model->getCasts());
        $this->assertEquals('date', $model->getCasts()['start_date'] ?? $model->getCasts()['start_date']);

        $this->assertArrayHasKey('end_date', $model->getCasts());
        $this->assertEquals('date', $model->getCasts()['end_date'] ?? $model->getCasts()['end_date']);
    }

    public function test_competition_belongs_to_season()
    {
        $season = Season::factory()->create();
        $competition = Competition::factory()->create(['season_id' => $season->id]);

        $this->assertInstanceOf(Season::class, $competition->season);
        $this->assertEquals($season->id, $competition->season->id);
    }

    public function test_competition_belongs_to_league()
    {
        $league = League::factory()->create();
        $competition = Competition::factory()->create(['league_id' => $league->id]);

        $this->assertInstanceOf(League::class, $competition->league);
        $this->assertEquals($league->id, $competition->league->id);
    }

    public function test_competition_belongs_to_category()
    {
        $category = Category::factory()->create();
        $competition = Competition::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $competition->category);
        $this->assertEquals($category->id, $competition->category->id);
    }

    public function test_competition_has_many_games()
    {
        $competition = Competition::factory()->create();
        $game = Game::factory()->create(['competition_id' => $competition->id]);

        $this->assertTrue($competition->games->contains($game));
        $this->assertInstanceOf(Game::class, $competition->games->first());
    }

    public function test_competition_has_roster_players_scoped_by_tenant()
    {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);

        $competition = new Competition();
        $competition->league_id = $league->id;
        $competition->season_id = $season->id;
        $competition->category_id = $category->id;
        $competition->name = 'Test Tournament';
        $competition->status = 'active';
        $competition->save();

        $team = Team::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['team_id' => $team->id, 'league_id' => $league->id]);

        \Filament\Facades\Filament::shouldReceive('getTenant')->andReturn($team);

        // Attach player to team/season explicitly
        \Illuminate\Support\Facades\DB::table('team_player_season')->insert([
            'team_id' => $team->id,
            'player_id' => $player->id,
            'season_id' => $season->id,
            'number' => 10,
            'position' => 'P',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertCount(1, $competition->roster_players);
        $this->assertEquals($player->id, $competition->roster_players->first()->id);
    }
}
