<?php

namespace Tests\Feature\Filament;

use App\Models\Category;
use App\Models\League;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryResourceTest extends TestCase
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

    public function test_can_create_category()
    {
        $category = Category::create([
            'name' => 'Test Category ' . uniqid(),
            'description' => 'A category for testing',
            'min_age' => 10,
            'max_age' => 15,
            'gender' => 'mixed',
            'league_id' => $this->league->id,
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
            'league_id' => $this->league->id,
        ]);
    }

    public function test_can_edit_category()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Name ' . uniqid();

        $category->update(['name' => $newName]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_category()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $categoryId = $category->id;

        $category->delete();

        $this->assertDatabaseMissing('categories', [
            'id' => $categoryId,
        ]);
    }

    public function test_category_belongs_to_league()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);

        $this->assertEquals($this->league->id, $category->league_id);
    }
}
