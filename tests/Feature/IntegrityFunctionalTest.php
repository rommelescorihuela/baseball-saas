<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\League;
use App\Models\Team;
use App\Models\Category;
use App\Models\Season;
use App\Models\Competition;
use App\Models\Player;
use App\Models\PlayerGameStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrityFunctionalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function deleting_a_player_cleans_up_stats_and_roster()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create(['league_id' => $league->id]);
        $player = Player::factory()->create(['team_id' => $team->id, 'league_id' => $league->id]);

        $season = Season::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $competition = \App\Models\Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        // Add to roster
        $team->players()->attach($player->id, ['season_id' => $season->id]);

        // Add stats
        $game = \App\Models\Game::factory()->create([
            'league_id' => $league->id,
            'competition_id' => $competition->id,
            'home_team_id' => $team->id,
        ]);
        PlayerGameStat::create([
            'game_id' => $game->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'ab' => 1,
        ]);

        $this->assertDatabaseHas('team_player_season', ['player_id' => $player->id]);
        $this->assertDatabaseHas('player_game_stats', ['player_id' => $player->id]);

        // Delete player
        $player->delete();

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        $this->assertDatabaseMissing('team_player_season', ['player_id' => $player->id]);
        $this->assertDatabaseMissing('player_game_stats', ['player_id' => $player->id]);
    }

    /** @test */
    public function deleting_a_team_cleans_up_registrations_and_roster()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create(['league_id' => $league->id]);
        $category = Category::factory()->create(['league_id' => $league->id]);

        // Register team to category
        $category->teams()->attach($team->id, ['status' => 'approved']);

        $player = Player::factory()->create(['team_id' => $team->id, 'league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $team->players()->attach($player->id, ['season_id' => $season->id]);

        $this->assertDatabaseHas('category_team', ['team_id' => $team->id]);
        $this->assertDatabaseHas('team_player_season', ['team_id' => $team->id]);

        // Delete team
        $team->delete();

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
        $this->assertDatabaseMissing('category_team', ['team_id' => $team->id]);
        $this->assertDatabaseMissing('team_player_season', ['team_id' => $team->id]);
    }
}
