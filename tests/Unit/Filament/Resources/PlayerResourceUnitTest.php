<?php

namespace Tests\Unit\Filament\Resources;

use App\Filament\App\Resources\Players\PlayerResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Tests\TestCase;

class PlayerResourceUnitTest extends TestCase
{
    private $livewire;

    protected function setUp(): void
    {
        parent::setUp();

        // Evitar issues con scopes usando MultiTenancy en boot de forms estaticos
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('app'));

        $this->livewire = new class extends Component implements HasForms, HasTable {
            use InteractsWithForms;
            use InteractsWithTable;
        };
    }

    public function test_player_resource_form_schema()
    {
        $schema = Schema::make($this->livewire);
        $schema->operation('create');
        $playerForm = PlayerResource::form($schema);

        $fields = $playerForm->getFlatFields();

        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('last_name', $fields);
        $this->assertArrayHasKey('number', $fields);
        $this->assertArrayHasKey('date_of_birth', $fields);
        $this->assertArrayHasKey('position', $fields);
        $this->assertArrayHasKey('team_id', $fields);

        $this->assertTrue($fields['name']->isRequired());
    }

    public function test_player_resource_table_schema()
    {
        $table = Table::make($this->livewire);
        $playerTable = PlayerResource::table($table);

        $columns = $playerTable->getColumns();

        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('last_name', $columns);
        $this->assertArrayHasKey('number', $columns);
        $this->assertArrayHasKey('position', $columns);
        $this->assertArrayHasKey('team.name', $columns);

        $this->assertTrue($columns['name']->isSearchable());
        $this->assertTrue($columns['last_name']->isSearchable());
        $this->assertTrue($columns['position']->isSearchable());
    }
}
