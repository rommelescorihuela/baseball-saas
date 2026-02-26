<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\League;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    // use RefreshDatabase;

    public function test_category_model_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('categories', [
                'id',
                'league_id',
                'name',
                'created_at',
                'updated_at',
            ])
        );
    }

    public function test_category_model_fillable_attributes()
    {
        $category = new Category();

        $expectedFillable = [
            'league_id',
            'name',
        ];

        $this->assertEquals($expectedFillable, $category->getFillable());
    }

    public function test_category_belongs_to_league()
    {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);

        $this->assertInstanceOf(League::class, $category->league);
        $this->assertEquals($league->id, $category->league->id);
    }

    public function test_category_belongs_to_many_teams()
    {
        $category = Category::factory()->create();
        $team = Team::factory()->create();

        $category->teams()->attach($team);

        $this->assertTrue($category->teams->contains($team));
        $this->assertInstanceOf(Team::class, $category->teams->first());
    }
}
