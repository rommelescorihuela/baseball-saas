<?php

namespace Tests\Unit\Filament\Resources;

use App\Filament\App\Resources\TeamResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Tests\TestCase;

class TeamResourceUnitTest extends TestCase
{
    private $livewire;

    protected function setUp(): void
    {
        parent::setUp();

        // Creamos un dummy view class con operation 'create' si hara falta
        $this->livewire = new class extends Component implements HasForms, HasTable {
            use InteractsWithForms;
            use InteractsWithTable;

            // Filament checks this property sometimes
            public $mountedActions = [];
        };
    }

    public function test_team_resource_form_schema()
    {
        $schema = Schema::make($this->livewire);
        // Simulamos el operation para que los campos 'owner_name' sean evaluados
        $schema->operation('create');
        $teamForm = TeamResource::form($schema);

        $fields = $teamForm->getFlatFields();

        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('owner_name', $fields);
        $this->assertArrayHasKey('owner_email', $fields);

        $this->assertTrue($fields['name']->isRequired());
        $this->assertTrue($fields['owner_email']->isRequired());
    }

    public function test_team_resource_table_schema()
    {
        $table = Table::make($this->livewire);
        $teamTable = TeamResource::table($table);

        $columns = $teamTable->getColumns();

        $this->assertArrayHasKey('name', $columns);
        // slug no es una column en TeamResource
        $this->assertArrayHasKey('created_at', $columns);

        $this->assertTrue($columns['name']->isSearchable());
        // El default de isSortable() no es observable directamente si no está aplicado explícitamente a name
    }

    public function test_team_resource_actions_and_pages()
    {
        $pages = TeamResource::getPages();

        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }
}
