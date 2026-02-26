<?php

namespace Tests\Feature\Filament;

use App\Filament\App\Resources\TeamResource;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamResourceTest extends TestCase
{
    private $user;
    private $league;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'team_owner']);

        $this->user = User::factory()->create();
        $this->league = League::factory()->create();
        $this->user->leagues()->attach($this->league);

        $this->actingAs($this->user);

        // Filament Tenancy Context
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->league);
    }

    public function test_can_render_team_resource_index_page()
    {
        $this->get(TeamResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_create_team()
    {
        $newData = [
            'name' => 'Test Team ' . uniqid(),
            'owner_name' => 'Manager Tester',
            'owner_email' => 'manager@test.com',
            'league_id' => $this->league->id,
        ];

        Livewire::test(TeamResource\Pages\CreateTeam::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('teams', [
            'name' => $newData['name'],
            'league_id' => $this->league->id,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $newData['owner_email'],
        ]);
    }

    public function test_can_edit_team()
    {
        $team = Team::factory()->create(['league_id' => $this->league->id]);
        $newName = 'Updated Team Name ' . uniqid();

        Livewire::test(TeamResource\Pages\EditTeam::class, [
            'record' => $team->id,
        ])
            ->fillForm([
                'name' => $newName,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_team()
    {
        $team = Team::factory()->create(['league_id' => $this->league->id]);

        Livewire::test(TeamResource\Pages\EditTeam::class, [
            'record' => $team->id,
        ])
            ->callAction('delete');

        $this->assertDatabaseMissing('teams', [
            'id' => $team->id,
        ]);
    }
}
