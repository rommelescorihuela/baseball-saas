<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\League;
use App\Models\Team;
use App\Models\Category;
use App\Models\Season;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Filament\App\Resources\GameResource;
use App\Filament\App\Resources\GameResource\Pages\ManualBoxScore;
use App\Filament\App\Resources\GameResource\RelationManagers\PlayerGameStatsRelationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;

class ScorerFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'scorer', 'guard_name' => 'web']);
        Filament::setCurrentPanel(Filament::getPanel('app'));
    }

    /** @test */
    public function scorer_can_update_game_score_manually()
    {
        $league = League::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('scorer');
        $admin->leagues()->attach($league->id);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);

        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'season_id' => $season->id,
            'category_id' => $category->id
        ]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'competition_id' => $competition->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'status' => 'scheduled'
        ]);

        $this->actingAs($admin);
        Filament::setTenant($league);

        Livewire::withQueryParams(['tenant' => $league->slug])
            ->test(ManualBoxScore::class, ['record' => $game->id])
            ->fillForm([
                'home_score' => 5,
                'visitor_score' => 3,
                'status' => 'finished',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(GameResource::getUrl('index'));

        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'home_score' => 5,
            'visitor_score' => 3,
            'status' => 'finished'
        ]);
    }

    /** @test */
    public function scorer_can_add_player_stats_to_a_game()
    {
        $league = League::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('scorer');
        $admin->leagues()->attach($league->id);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);

        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create(['league_id' => $league->id]);

        $game = Game::factory()->create([
            'league_id' => $league->id,
            'competition_id' => $competition->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
        ]);

        $player = Player::factory()->create(['team_id' => $homeTeam->id, 'league_id' => $league->id]);

        $this->actingAs($admin);
        Filament::setTenant($league);

        Livewire::withQueryParams(['tenant' => $league->slug])
            ->test(PlayerGameStatsRelationManager::class, [
                'ownerRecord' => $game,
                'pageClass' => GameResource\Pages\EditGame::class,
            ])
            ->callTableAction('create', data: [
                'player_id' => $player->id,
                'team_id' => $homeTeam->id,
                'ab' => 4,
                'h' => 2,
                'r' => 1,
                'rbi' => 2,
                'hr' => 1,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('player_game_stats', [
            'game_id' => $game->id,
            'player_id' => $player->id,
            'ab' => 4,
            'h' => 2,
            'hr' => 1
        ]);

        $stat = PlayerGameStat::where('game_id', $game->id)->where('player_id', $player->id)->first();
        $this->assertEquals('0.500', $stat->avg);
    }
}
