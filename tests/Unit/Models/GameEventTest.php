<?php

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\GameEvent;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GameEventTest extends TestCase
{
    // use RefreshDatabase;

    public function test_game_event_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('game_events', [
                'id',
                'game_id',
                'inning',
                'is_top_inning',
                'team_id',
                'batter_id',
                'pitcher_id',
                'outs_before',
                'balls_before',
                'strikes_before',
                'type',
                'result',
                'runs_scored',
                'created_by',
                'created_at',
                'updated_at',
            ])
        );
    }

    public function test_game_event_fillable_attributes()
    {
        $event = new GameEvent();

        $expected = [
            'game_id',
            'inning',
            'is_top_inning',
            'team_id',
            'batter_id',
            'pitcher_id',
            'outs_before',
            'balls_before',
            'strikes_before',
            'type',
            'result',
            'runs_scored',
            'created_by',
        ];

        $this->assertEquals($expected, $event->getFillable());
    }

    public function test_game_event_has_casts()
    {
        $event = new GameEvent();

        $this->assertArrayHasKey('is_top_inning', $event->getCasts());
        $this->assertEquals('boolean', $event->getCasts()['is_top_inning']);

        $this->assertArrayHasKey('result', $event->getCasts());
        $this->assertEquals('array', $event->getCasts()['result']);
    }

    public function test_game_event_belongs_to_game()
    {
        // Since GameEvent DOES NOT have a factory right now in this app, we will use basic create
        $game = Game::factory()->create();
        $event = new GameEvent(['game_id' => $game->id]);

        $this->assertInstanceOf(Game::class, $event->game()->getRelated());
    }

    public function test_game_event_belongs_to_batter()
    {
        $player = Player::factory()->create();
        $event = new GameEvent(['batter_id' => $player->id]);

        $this->assertInstanceOf(Player::class, $event->batter()->getRelated());
    }

    public function test_game_event_belongs_to_pitcher()
    {
        $player = Player::factory()->create();
        $event = new GameEvent(['pitcher_id' => $player->id]);

        $this->assertInstanceOf(Player::class, $event->pitcher()->getRelated());
    }

    public function test_game_event_belongs_to_team()
    {
        $team = Team::factory()->create();
        $event = new GameEvent(['team_id' => $team->id]);

        $this->assertInstanceOf(Team::class, $event->team()->getRelated());
    }

    public function test_game_event_belongs_to_creator()
    {
        $user = User::factory()->create();
        $event = new GameEvent(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $event->creator()->getRelated());
    }
}
