<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Competition;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use App\Models\PlayerGameStat;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles and Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@baseball-saas.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole($superAdminRole);

        $roles = [
            'panel_user',
            'league_owner',
            'team_owner',
            'secretary',
            'coach',
            'player',
            'scorer'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2. SaaS Demo Data (Leagues, Teams, Players, Seasons, Competitions, Games, Stats)
        $leaguesCount = 5;
        $teamsPerLeague = 12;
        $playersPerTeam = 15;
        $categoriesPerLeague = 4;

        $leagues = League::factory()->count($leaguesCount)->create();

        foreach ($leagues as $league) {
            // League Owner
            $leagueOwner = User::factory()->create([
                'name' => 'League Owner ' . $league->name,
                'email' => 'league-owner@' . $league->slug . '.com',
                'password' => Hash::make('password'),
            ]);
            $league->users()->attach($leagueOwner);

            // Teams
            for ($i = 1; $i <= $teamsPerLeague; $i++) {
                $teamName = $league->name . ' Team ' . $i;
                $team = Team::create([
                    'league_id' => $league->id,
                    'name' => $teamName,
                    'slug' => Str::slug($teamName . '-' . uniqid()),
                    'logo' => null,
                ]);

                // Create Academy Owner for this team
                $teamOwner = User::factory()->create([
                    'name' => 'Academy Owner ' . $team->name,
                    'email' => 'team-owner@' . $team->slug . '.com',
                    'password' => Hash::make('password'),
                ]);
                $team->users()->attach($teamOwner);
                $teamOwner->assignRole('team_owner');

                // Players for this team
                for ($p = 1; $p <= $playersPerTeam; $p++) {
                    Player::create([
                        'team_id' => $team->id,
                        'league_id' => $league->id,
                        'name' => 'Player ' . $p,
                        'last_name' => $team->name,
                        'number' => rand(1, 99),
                        'position' => ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF'][rand(0, 8)],
                    ]);
                }
            }

            // Reference teams for categories
            $teams = $league->teams;

            // Categories
            $categories = Category::factory()->count($categoriesPerLeague)->create([
                'league_id' => $league->id,
            ]);

            // Distribute teams into categories
            $shuffledTeams = $teams->shuffle();
            $chunks = $shuffledTeams->split($categories->count());

            foreach ($categories as $index => $category) {
                $teamsInCat = $chunks->get($index);
                if ($teamsInCat) {
                    $category->teams()->attach($teamsInCat->pluck('id')->toArray(), ['status' => 'approved']);
                }
            }

            // Seasons
            $seasons = Season::factory()->count(2)->sequence(
                ['is_active' => true, 'name' => 'Season 2026'],
                ['is_active' => false, 'name' => 'Season 2025']
            )->create([
                        'league_id' => $league->id,
                    ]);

            foreach ($seasons as $season) {
                foreach ($categories as $category) {
                    $competition = Competition::factory()->create([
                        'season_id' => $season->id,
                        'category_id' => $category->id,
                        'name' => 'Torneo ' . $category->name,
                    ]);

                    // Roster for Season
                    foreach ($category->teams as $team) {
                        foreach ($team->players as $player) {
                            DB::table('team_player_season')->insertOrIgnore([
                                'team_id' => $team->id,
                                'player_id' => $player->id,
                                'season_id' => $season->id,
                                'number' => $player->number,
                                'position' => $player->position,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    // Games
                    $teamsInComp = $category->teams;
                    if ($teamsInComp->count() >= 2) {
                        $games = Game::factory()->count(10)->create([
                            'league_id' => $league->id,
                            'category_id' => $category->id,
                            'competition_id' => $competition->id,
                            'home_team_id' => $teamsInComp->random()->id,
                            'visitor_team_id' => $teamsInComp->random()->id,
                        ]);

                        foreach ($games->take(5) as $game) {
                            if ($game->home_team_id === $game->visitor_team_id) {
                                $game->visitor_team_id = $teamsInComp->where('id', '!=', $game->home_team_id)->random()->id;
                            }

                            $homeScore = rand(1, 15);
                            $visitorScore = rand(1, 15);

                            $game->update([
                                'status' => 'finished',
                                'home_score' => $homeScore,
                                'visitor_score' => $visitorScore,
                                'start_time' => now()->subDays(rand(1, 30)),
                            ]);

                            // Stats
                            $players = Player::whereIn('team_id', [$game->home_team_id, $game->visitor_team_id])->get();
                            foreach ($players->random(min(10, $players->count())) as $player) {
                                PlayerGameStat::create([
                                    'player_id' => $player->id,
                                    'game_id' => $game->id,
                                    'team_id' => $player->team_id,
                                    'ab' => rand(3, 5),
                                    'h' => rand(0, 3),
                                    'r' => rand(0, 2),
                                    'rbi' => rand(0, 3),
                                    'ip' => $player->position === 'P' ? rand(1, 9) : 0,
                                    'p_r' => $player->position === 'P' ? rand(0, 5) : 0,
                                    'p_so' => rand(0, 10),
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}