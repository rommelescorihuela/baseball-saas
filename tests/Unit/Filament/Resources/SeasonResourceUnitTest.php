<?php

namespace Tests\Unit\Filament\Resources;

use App\Filament\App\Resources\SeasonResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Tests\TestCase;

class SeasonResourceUnitTest extends TestCase
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

    public function test_season_resource_form_schema()
    {
        $schema = Schema::make($this->livewire);
        $schema->operation('create');
        $seasonForm = SeasonResource::form($schema);

        $fields = $seasonForm->getFlatFields();

        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('start_date', $fields);
        $this->assertArrayHasKey('end_date', $fields);
        $this->assertArrayHasKey('is_active', $fields);

        $this->assertTrue($fields['name']->isRequired());
    }

    public function test_season_resource_table_schema()
    {
        $table = Table::make($this->livewire);
        $seasonTable = SeasonResource::table($table);

        $columns = $seasonTable->getColumns();

        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('start_date', $columns);
        $this->assertArrayHasKey('end_date', $columns);
        $this->assertArrayHasKey('is_active', $columns);

        $this->assertTrue($columns['name']->isSearchable());
        $this->assertTrue($columns['start_date']->isSortable());
    }
}
