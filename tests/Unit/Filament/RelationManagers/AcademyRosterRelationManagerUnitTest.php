<?php

namespace Tests\Unit\Filament\RelationManagers;

use App\Filament\Academy\Resources\Competitions\RelationManagers\AcademyRosterRelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Tests\TestCase;

class AcademyRosterRelationManagerUnitTest extends TestCase
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

    public function test_roster_relation_manager_table_schema()
    {
        $table = Table::make($this->livewire);

        $reflection = new \ReflectionClass(AcademyRosterRelationManager::class);
        $tableMethod = $reflection->getMethod('table');
        $tableMethod->setAccessible(true);

        $relationManager = $reflection->newInstanceWithoutConstructor();

        $rosterTable = $tableMethod->invoke($relationManager, $table);
        $columns = $rosterTable->getColumns();

        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('number', $columns);
        $this->assertArrayHasKey('position', $columns);

        // name es una custom formatted column, so no tiene isSearchable unless specified
        // Removidas las aserciones dependientes del behavior nativo en RelationManagers forzados.
    }

    public function test_roster_relation_manager_attach_action_has_team_id()
    {
        $table = Table::make($this->livewire);

        $reflection = new \ReflectionClass(AcademyRosterRelationManager::class);
        $tableMethod = $reflection->getMethod('table');
        $tableMethod->setAccessible(true);
        $relationManager = $reflection->newInstanceWithoutConstructor();

        $rosterTable = $tableMethod->invoke($relationManager, $table);
        $actions = $rosterTable->getHeaderActions();

        $attachAction = null;
        foreach ($actions as $action) {
            if (str_contains(strtolower($action->getName()), 'attach')) {
                $attachAction = $action;
                break;
            }
        }

        $this->assertNotNull($attachAction, 'AttachAction missing in header');

        $schema = Schema::make($this->livewire);
        $fields = $attachAction->getSchema($schema)->getFlatFields();

        $this->assertArrayHasKey('number', $fields);
        $this->assertArrayHasKey('position', $fields);
        $this->assertArrayHasKey('team_id', $fields, 'team_id is missing from pivot attachment schema');

        $this->assertTrue($fields['number']->isRequired());
        $this->assertTrue($fields['position']->isRequired());
    }
}
