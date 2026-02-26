<?php

namespace Tests\Feature\Workflows;

use App\Filament\App\Resources\TeamResource\RelationManagers\SeasonRosterRelationManager;
use App\Models\League;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

class PlayerRosterTest extends TestCase
{
    private $user;
    private $league;
    private $team;
    private $season;

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

        $this->season = Season::factory()->create([
            'league_id' => $this->league->id,
            'is_active' => true,
        ]);

        $this->team = Team::factory()->create([
            'league_id' => $this->league->id,
        ]);
    }

    public function test_can_render_roster_relation_manager()
    {
        Livewire::test(SeasonRosterRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => \App\Filament\App\Resources\TeamResource\Pages\EditTeam::class,
        ])
            ->assertSuccessful();
    }

    public function test_can_attach_player_to_team_roster()
    {
        // Require a player in the same league
        $player = Player::factory()->create([
            'league_id' => $this->league->id,
        ]);

        Livewire::test(SeasonRosterRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => \App\Filament\App\Resources\TeamResource\Pages\EditTeam::class,
        ])
            ->callTableAction('attach', null, [
                'recordId' => $player->id,
                'season_id' => $this->season->id,
                'number' => 24,
                'position' => 'CF',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('team_player_season', [
            'team_id' => $this->team->id,
            'player_id' => $player->id,
            'season_id' => $this->season->id,
            'number' => 24,
            'position' => 'CF',
        ]);
    }

    public function test_can_edit_player_pivot_data_on_roster()
    {
        $player = Player::factory()->create([
            'league_id' => $this->league->id,
        ]);

        $this->team->players()->attach($player->id, [
            'season_id' => $this->season->id,
            'number' => 10,
            'position' => 'SS',
        ]);

        Livewire::test(SeasonRosterRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => \App\Filament\App\Resources\TeamResource\Pages\EditTeam::class,
        ])
            ->callTableAction('edit', $player->id, [
                'number' => 99,
                'position' => '1B',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('team_player_season', [
            'team_id' => $this->team->id,
            'player_id' => $player->id,
            'season_id' => $this->season->id,
            'number' => 99,
            'position' => '1B',
        ]);
    }

    public function test_can_detach_player_from_team_roster()
    {
        $player = Player::factory()->create([
            'league_id' => $this->league->id,
        ]);

        $this->team->players()->attach($player->id, [
            'season_id' => $this->season->id,
            'number' => 10,
            'position' => 'SS',
        ]);

        Livewire::test(SeasonRosterRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => \App\Filament\App\Resources\TeamResource\Pages\EditTeam::class,
        ])
            ->callTableAction('detach', $player->id)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('team_player_season', [
            'team_id' => $this->team->id,
            'player_id' => $player->id,
        ]);
    }
}
