<?php

namespace Tests\Unit\Models;

use App\Models\Competition;
use App\Models\League;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SeasonTest extends TestCase
{
    // use RefreshDatabase;

    public function test_season_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('seasons', [
                'id',
                'league_id',
                'name',
                'start_date',
                'end_date',
                'is_active',
                'created_at',
                'updated_at',
            ])
        );
    }

    public function test_season_fillable_attributes()
    {
        $model = new Season();
        $expected = [
            'league_id',
            'name',
            'start_date',
            'end_date',
            'is_active'
        ];

        $this->assertEquals($expected, $model->getFillable());
    }

    public function test_season_has_casts()
    {
        $model = new Season();

        $this->assertArrayHasKey('start_date', $model->getCasts());
        $this->assertEquals('date', $model->getCasts()['start_date'] ?? $model->getCasts()['start_date']);

        $this->assertArrayHasKey('end_date', $model->getCasts());
        $this->assertEquals('date', $model->getCasts()['end_date'] ?? $model->getCasts()['end_date']);

        $this->assertArrayHasKey('is_active', $model->getCasts());
        $this->assertEquals('boolean', $model->getCasts()['is_active']);
    }

    public function test_season_belongs_to_league()
    {
        $league = League::factory()->create();
        $season = Season::factory()->create(['league_id' => $league->id]);

        $this->assertInstanceOf(League::class, $season->league);
        $this->assertEquals($league->id, $season->league->id);
    }

    public function test_season_has_many_competitions()
    {
        $season = Season::factory()->create();
        $competition = Competition::factory()->create(['season_id' => $season->id]);

        $this->assertTrue($season->competitions->contains($competition));
        $this->assertInstanceOf(Competition::class, $season->competitions->first());
    }
}
