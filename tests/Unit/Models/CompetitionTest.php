<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    // use RefreshDatabase;

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
}
