<?php

namespace Tests\Feature\Filament;

use App\Filament\App\Resources\CategoryResource;
use App\Models\Category;
use App\Models\League;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    private $user;
    private $league;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->league = League::factory()->create();
        $this->user->leagues()->attach($this->league);

        $this->actingAs($this->user);

        // Filament Tenancy Context
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->league);
    }

    public function test_can_render_category_resource_index_page()
    {
        $this->get(CategoryResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_create_category()
    {
        $newData = [
            'name' => 'Test Category ' . uniqid(),
            'description' => 'A category for testing',
            'min_age' => 10,
            'max_age' => 15,
            'gender' => 'mixed',
            'league_id' => $this->league->id,
        ];

        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => $newData['name'],
            'league_id' => $this->league->id,
        ]);
    }

    public function test_can_edit_category()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Name ' . uniqid();

        Livewire::test(CategoryResource\Pages\EditCategory::class, [
            'record' => $category->id,
        ])
            ->fillForm([
                'name' => $newName,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_category()
    {
        $category = Category::factory()->create(['league_id' => $this->league->id]);

        Livewire::test(CategoryResource\Pages\EditCategory::class, [
            'record' => $category->id,
        ])
            ->callAction('delete');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
