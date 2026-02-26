<?php

namespace Tests\Feature\Workflows;

use App\Livewire\GameScoring;
use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GameScoringTest extends TestCase
{
    // use RefreshDatabase;

    private $user;
    private $league;
    private $game;
    private $homeTeam;
    private $visitorTeam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->league = League::factory()->create();
        $this->user->leagues()->attach($this->league);

        $this->actingAs($this->user);

        $category = Category::factory()->create(['league_id' => $this->league->id]);
        $competition = Competition::factory()->create(['league_id' => $this->league->id]);

        $this->homeTeam = Team::factory()->create(['league_id' => $this->league->id]);
        $this->visitorTeam = Team::factory()->create(['league_id' => $this->league->id]);

        $season = \App\Models\Season::factory()->create(['league_id' => $this->league->id]);

        // Add some players to homeTeam and visitorTeam via pivot table
        $homePlayers = Player::factory()->count(3)->create(['league_id' => $this->league->id]);
        foreach ($homePlayers as $player) {
            $this->homeTeam->players()->attach($player->id, ['season_id' => $season->id]);
        }

        $visitorPlayers = Player::factory()->count(3)->create(['league_id' => $this->league->id]);
        foreach ($visitorPlayers as $player) {
            $this->visitorTeam->players()->attach($player->id, ['season_id' => $season->id]);
        }

        $this->game = Game::factory()->create([
            'league_id' => $this->league->id,
            'category_id' => $category->id,
            'competition_id' => $competition->id,
            'home_team_id' => $this->homeTeam->id,
            'visitor_team_id' => $this->visitorTeam->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_can_load_game_scoring_component()
    {
        Livewire::test(GameScoring::class, ['game' => $this->game])
            ->assertSet('inning', 1)
            ->assertSet('is_top_inning', true)
            ->assertSet('outs', 0);
    }

    public function test_three_strikes_make_an_out()
    {
        $batter = $this->visitorTeam->players->first();
        $pitcher = $this->homeTeam->players->first();

        Livewire::test(GameScoring::class, ['game' => $this->game])
            ->set('batter_id', $batter->id)
            ->set('pitcher_id', $pitcher->id)
            ->call('registerEvent', 'pitch', 'strike', 0)
            ->assertSet('strikes', 1)
            ->assertSet('outs', 0)
            ->call('registerEvent', 'pitch', 'strike', 0)
            ->assertSet('strikes', 2)
            ->assertSet('outs', 0)
            ->call('registerEvent', 'pitch', 'strike', 0)
            ->assertSet('strikes', 0)
            ->assertSet('outs', 1);
    }

    public function test_three_outs_change_half_inning()
    {
        $batter = $this->visitorTeam->players->first();
        $pitcher = $this->homeTeam->players->first();

        Livewire::test(GameScoring::class, ['game' => $this->game])
            ->set('batter_id', $batter->id)
            ->set('pitcher_id', $pitcher->id)
            // Out 1
            ->call('registerEvent', 'play', 'out', 0)
            ->assertSet('outs', 1)
            // Out 2
            ->call('registerEvent', 'play', 'strikeout', 0)
            ->assertSet('outs', 2)
            ->assertSet('is_top_inning', true)
            // Out 3 -> Changes to Bottom 1st
            ->call('registerEvent', 'play', 'out', 0)
            ->assertSet('outs', 0)
            ->assertSet('is_top_inning', false)
            ->assertSet('inning', 1);
    }

    public function test_home_run_clears_bases_and_adds_runs()
    {
        $batter = $this->visitorTeam->players->first();
        $pitcher = $this->homeTeam->players->first();

        // Runner on first and second
        $runner1 = $this->visitorTeam->players[1];
        $runner2 = $this->visitorTeam->players[2];

        Livewire::test(GameScoring::class, ['game' => $this->game])
            ->set('batter_id', $batter->id)
            ->set('pitcher_id', $pitcher->id)
            ->set('runner_on_first', $runner1->id)
            ->set('runner_on_second', $runner2->id)
            ->call('registerEvent', 'play', 'hr', 3) // 3 RBI
            ->assertSet('runner_on_first', null)
            ->assertSet('runner_on_second', null)
            ->assertSet('runner_on_third', null);

        // Let's assert DB event has 3 runs_scored
        $this->assertDatabaseHas('game_events', [
            'game_id' => $this->game->id,
            'type' => 'play',
            'runs_scored' => 3,
        ]);
    }

    public function test_finish_game_updates_status()
    {
        Livewire::test(GameScoring::class, ['game' => $this->game])
            ->call('finishGame');

        $this->assertEquals('finished', $this->game->fresh()->status);
    }
}
