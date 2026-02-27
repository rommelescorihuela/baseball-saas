<?php

namespace Tests\Unit\Filament\Resources;

use App\Filament\App\Resources\GameResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Tests\TestCase;

class GameResourceUnitTest extends TestCase
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

    public function test_game_resource_form_schema()
    {
        $schema = Schema::make($this->livewire);
        $schema->operation('create');
        $gameForm = GameResource::form($schema);

        $fields = $gameForm->getFlatFields();

        $this->assertArrayHasKey('competition_id', $fields);
        $this->assertArrayHasKey('category_id', $fields);
        $this->assertArrayHasKey('home_team_id', $fields);
        $this->assertArrayHasKey('visitor_team_id', $fields);
        $this->assertArrayHasKey('start_time', $fields);
        $this->assertArrayHasKey('location', $fields);
        $this->assertArrayHasKey('status', $fields);
        $this->assertArrayHasKey('home_score', $fields);
        $this->assertArrayHasKey('visitor_score', $fields);

        $this->assertTrue($fields['competition_id']->isRequired());
        $this->assertTrue($fields['home_team_id']->isRequired());
    }

    public function test_game_resource_table_schema()
    {
        $table = Table::make($this->livewire);
        $gameTable = GameResource::table($table);

        $columns = $gameTable->getColumns();

        $this->assertArrayHasKey('start_time', $columns);
        $this->assertArrayHasKey('category.name', $columns);
        $this->assertArrayHasKey('homeTeam.name', $columns);
        $this->assertArrayHasKey('visitorTeam.name', $columns);
        $this->assertArrayHasKey('status', $columns);

        $this->assertTrue($columns['start_time']->isSortable());
    }

    public function test_game_resource_actions_and_pages()
    {
        $pages = GameResource::getPages();

        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }
}
