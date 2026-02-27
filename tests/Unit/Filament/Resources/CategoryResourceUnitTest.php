<?php

namespace Tests\Unit\Filament\Resources;

use App\Filament\App\Resources\CategoryResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Tests\TestCase;

class CategoryResourceUnitTest extends TestCase
{
    private $livewire;

    protected function setUp(): void
    {
        parent::setUp();

        $this->livewire = new class extends Component implements HasForms, HasTable {
            use InteractsWithForms;
            use InteractsWithTable;
        };
    }

    public function test_category_resource_form_schema()
    {
        $schema = Schema::make($this->livewire);
        $schema->operation('create');
        $categoryForm = CategoryResource::form($schema);

        $fields = $categoryForm->getFlatFields();

        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('description', $fields);

        $this->assertTrue($fields['name']->isRequired());
    }

    public function test_category_resource_table_schema()
    {
        $table = Table::make($this->livewire);
        $categoryTable = CategoryResource::table($table);

        $columns = $categoryTable->getColumns();

        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('created_at', $columns);

        $this->assertTrue($columns['name']->isSearchable());
    }
}
