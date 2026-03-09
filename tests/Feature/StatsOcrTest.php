<?php

use App\Models\Game;
use App\Models\Team;
use App\Models\Player;
use App\Models\League;
use App\Models\Season;
use App\Models\Category;
use App\Models\Competition;
use App\Services\StatsOcrService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

test('it can parse stats from gemini response and match players', function () {
    // Setup
    $league = League::factory()->create();
    $season = Season::factory()->create(['league_id' => $league->id]);
    $category = Category::factory()->create(['league_id' => $league->id]);
    $homeTeam = Team::factory()->create(['league_id' => $league->id, 'name' => 'Tigres']);
    $visitorTeam = Team::factory()->create(['league_id' => $league->id, 'name' => 'Leones']);
    
    $competition = Competition::factory()->create([
        'category_id' => $category->id,
        'season_id' => $season->id,
    ]);

    $player1 = Player::factory()->create(['name' => 'Juan Perez']);
    $player2 = Player::factory()->create(['name' => 'Pedro Lopez']);
    
    $homeTeam->players()->attach($player1, ['season_id' => $season->id]);
    $visitorTeam->players()->attach($player2, ['season_id' => $season->id]);

    $game = Game::factory()->create([
        'home_team_id' => $homeTeam->id,
        'visitor_team_id' => $visitorTeam->id,
        'competition_id' => $competition->id,
    ]);

    Config::set('services.gemini.key', 'fake-key');
    Config::set('services.gemini.enabled', true);

    // Mock Gemini API
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    [
                                        'player_name' => 'Juan Perez',
                                        'team_side' => 'home',
                                        'ab' => 4,
                                        'h' => 2,
                                        'rbi' => 1,
                                    ],
                                    [
                                        'player_name' => 'Pedro Lopez',
                                        'team_side' => 'visitor',
                                        'ab' => 3,
                                        'h' => 1,
                                        'rbi' => 0,
                                    ]
                                ])
                            ]
                        ]
                    ]
                ]
            ]
        ], 200),
    ]);

    // Execute
    $service = new StatsOcrService();
    // We pass a dummy path since we are mocking the HTTP call anyway
    $results = $service->processBoxScore(__FILE__, $game);

    // Assert
    expect($results)->toHaveCount(2);
    
    $result1 = collect($results)->firstWhere('player_name', 'Juan Perez');
    expect($result1['player_id'])->toBe($player1->id);
    expect($result1['team_id'])->toBe($homeTeam->id);
    expect($result1['stats']['ab'])->toBe(4);
    expect($result1['stats']['h'])->toBe(2);

    $result2 = collect($results)->firstWhere('player_name', 'Pedro Lopez');
    expect($result2['player_id'])->toBe($player2->id);
    expect($result2['team_id'])->toBe($visitorTeam->id);
    expect($result2['stats']['ab'])->toBe(3);
    expect($result2['stats']['h'])->toBe(1);
});
