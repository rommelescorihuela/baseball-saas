<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Game;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Services\StandingsCalculator;

describe('StandingsCalculator', function () {

    test('can be instantiated with competition', function () {
        $competition = Competition::factory()->create();
        $calculator = new StandingsCalculator($competition);

        expect($calculator)->toBeInstanceOf(StandingsCalculator::class);
    });

    test('returns empty collection when no finished games', function () {
        $competition = Competition::factory()->create();
        $calculator = new StandingsCalculator($competition);

        $standings = $calculator->calculate();

        expect($standings)->toBeEmpty();
    });

    test('calculates standings for single game', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $homeTeam = Team::factory()->create(['league_id' => $league->id]);
        $visitorTeam = Team::factory()->create(['league_id' => $league->id]);

        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $homeTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'home_score' => 5,
            'visitor_score' => 3,
            'status' => 'finished',
        ]);

        $calculator = new StandingsCalculator($competition);
        $standings = $calculator->calculate();

        expect($standings)->toHaveCount(2)
            ->and($standings->first()['team_id'])->toBe($homeTeam->id)
            ->and($standings->first()['wins'])->toBe(1)
            ->and($standings->first()['losses'])->toBe(0)
            ->and($standings->first()['runs_for'])->toBe(5)
            ->and($standings->first()['runs_against'])->toBe(3);
    });

    test('calculates winning percentage correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team1 = Team::factory()->create(['league_id' => $league->id]);
        $team2 = Team::factory()->create(['league_id' => $league->id]);

        // Team 1 wins 2 games
        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team1->id,
            'visitor_team_id' => $team2->id,
            'home_score' => 5,
            'visitor_score' => 3,
            'status' => 'finished',
        ]);

        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team2->id,
            'visitor_team_id' => $team1->id,
            'home_score' => 2,
            'visitor_score' => 4,
            'status' => 'finished',
        ]);

        $calculator = new StandingsCalculator($competition);
        $standings = $calculator->calculate();

        // Team 1 should be first with 2-0 record and 1.000 pct
        expect($standings->first()['team_id'])->toBe($team1->id)
            ->and($standings->first()['wins'])->toBe(2)
            ->and($standings->first()['losses'])->toBe(0)
            ->and($standings->first()['pct'])->toBe(1.0);
    });

    test('sorts standings by winning percentage descending', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team1 = Team::factory()->create(['league_id' => $league->id]);
        $team2 = Team::factory()->create(['league_id' => $league->id]);
        $team3 = Team::factory()->create(['league_id' => $league->id]);

        // Team 1: 2-0 (1.000)
        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team1->id,
            'visitor_team_id' => $team2->id,
            'home_score' => 5,
            'visitor_score' => 3,
            'status' => 'finished',
        ]);

        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team1->id,
            'visitor_team_id' => $team3->id,
            'home_score' => 4,
            'visitor_score' => 2,
            'status' => 'finished',
        ]);

        // Team 2: 1-1 (.500)
        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team2->id,
            'visitor_team_id' => $team3->id,
            'home_score' => 6,
            'visitor_score' => 4,
            'status' => 'finished',
        ]);

        $calculator = new StandingsCalculator($competition);
        $standings = $calculator->calculate();

        expect($standings)->toHaveCount(3)
            ->and($standings[0]['pct'])->toBe(1.0)
            ->and($standings[1]['pct'])->toBe(0.5)
            ->and($standings[2]['pct'])->toBe(0.0);
    });

    test('ignores non-finished games', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team1 = Team::factory()->create(['league_id' => $league->id]);
        $team2 = Team::factory()->create(['league_id' => $league->id]);

        // Create a scheduled game (not finished)
        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team1->id,
            'visitor_team_id' => $team2->id,
            'home_score' => 5,
            'visitor_score' => 3,
            'status' => 'scheduled',
        ]);

        $calculator = new StandingsCalculator($competition);
        $standings = $calculator->calculate();

        expect($standings)->toBeEmpty();
    });

    test('accumulates runs for and against correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $team1 = Team::factory()->create(['league_id' => $league->id]);
        $team2 = Team::factory()->create(['league_id' => $league->id]);

        // Game 1: Team1 scores 5, Team2 scores 3
        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team1->id,
            'visitor_team_id' => $team2->id,
            'home_score' => 5,
            'visitor_score' => 3,
            'status' => 'finished',
        ]);

        // Game 2: Team1 scores 4, Team2 scores 2
        Game::factory()->create([
            'competition_id' => $competition->id,
            'league_id' => $league->id,
            'category_id' => $category->id,
            'home_team_id' => $team2->id,
            'visitor_team_id' => $team1->id,
            'home_score' => 2,
            'visitor_score' => 4,
            'status' => 'finished',
        ]);

        $calculator = new StandingsCalculator($competition);
        $standings = $calculator->calculate();

        $team1Stats = $standings->firstWhere('team_id', $team1->id);
        $team2Stats = $standings->firstWhere('team_id', $team2->id);

        expect($team1Stats['runs_for'])->toBe(9)  // 5 + 4
            ->and($team1Stats['runs_against'])->toBe(5)  // 3 + 2
            ->and($team2Stats['runs_for'])->toBe(5)  // 3 + 2
            ->and($team2Stats['runs_against'])->toBe(9);  // 5 + 4
    });

    test('handles multiple teams correctly', function () {
        $league = League::factory()->create();
        $category = Category::factory()->create(['league_id' => $league->id]);
        $season = Season::factory()->create(['league_id' => $league->id]);
        $competition = Competition::factory()->create([
            'league_id' => $league->id,
            'category_id' => $category->id,
            'season_id' => $season->id,
        ]);

        $teams = Team::factory()->count(4)->create(['league_id' => $league->id]);

        // Create games between all teams
        foreach ($teams as $i => $team1) {
            foreach ($teams as $j => $team2) {
                if ($i < $j) {
                    Game::factory()->create([
                        'competition_id' => $competition->id,
                        'league_id' => $league->id,
                        'category_id' => $category->id,
                        'home_team_id' => $team1->id,
                        'visitor_team_id' => $team2->id,
                        'home_score' => rand(1, 10),
                        'visitor_score' => rand(1, 10),
                        'status' => 'finished',
                    ]);
                }
            }
        }

        $calculator = new StandingsCalculator($competition);
        $standings = $calculator->calculate();

        expect($standings)->toHaveCount(4);
    });
});
