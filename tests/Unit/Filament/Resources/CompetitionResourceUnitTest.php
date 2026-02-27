<?php

namespace Tests\Unit\Filament\Resources;

use App\Filament\App\Resources\CompetitionResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Tests\TestCase;

class CompetitionResourceUnitTest extends TestCase
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

    public function test_competition_resource_form_schema()
    {
        $schema = Schema::make($this->livewire);
        $schema->operation('create');
        $competitionForm = CompetitionResource::form($schema);

        $fields = $competitionForm->getFlatFields();

        $this->assertArrayHasKey('season_id', $fields);
        $this->assertArrayHasKey('category_id', $fields);
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('start_date', $fields);
        $this->assertArrayHasKey('end_date', $fields);
        $this->assertArrayHasKey('is_active', $fields);

        $this->assertTrue($fields['name']->isRequired());
        $this->assertTrue($fields['season_id']->isRequired());
    }

    public function test_competition_resource_table_schema()
    {
        $table = Table::make($this->livewire);
        $competitionTable = CompetitionResource::table($table);

        $columns = $competitionTable->getColumns();

        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('season.name', $columns);
        $this->assertArrayHasKey('category.name', $columns);
        $this->assertArrayHasKey('start_date', $columns);

        $this->assertTrue($columns['name']->isSearchable());
        $this->assertTrue($columns['season.name']->isSortable());
    }
}
