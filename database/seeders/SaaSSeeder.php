<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Competition;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SaaSSeeder extends Seeder
{
    public function run(): void
    {
        // Crear 5 Ligas
        $leagues = League::factory()->count(5)->create();

        foreach ($leagues as $league) {
            // Crear Owner para la Liga
            $owner = User::factory()->create([
                'name' => 'Owner ' . $league->name,
                'email' => 'owner@' . $league->slug . '.com',
                'password' => Hash::make('password'),
            ]);
            $league->users()->attach($owner);

            // Crear 12 Equipos por Liga
            $teams = Team::factory()->count(12)->create([
                'league_id' => $league->id,
            ]);

            // Crear 4 Categorías por Liga
            $categories = Category::factory()->count(4)->create([
                'league_id' => $league->id,
            ]);

            // Distribuir equipos en las categorías (3 equipos por categoría)
            $shuffledTeams = $teams->shuffle();
            $chunks = $shuffledTeams->split($categories->count());

            foreach ($categories as $index => $category) {
                $teamsInCat = $chunks->get($index);
                if ($teamsInCat) {
                    $category->teams()->attach($teamsInCat);
                }
            }

            foreach ($teams as $team) {
                \App\Models\Player::factory()->count(15)->create([
                    'team_id' => $team->id,
                ]);
            }

            // Crear 2 Temporadas (1 Activa, 1 Inactiva)
            $seasons = Season::factory()->count(2)->sequence(
                ['is_active' => true, 'name' => 'Season 2026'],
                ['is_active' => false, 'name' => 'Season 2025']
            )->create([
                'league_id' => $league->id,
            ]);

            foreach ($seasons as $season) {
                // Crear Competencias por Categoría para la Temporada
                foreach ($categories as $category) {
                    $competition = Competition::factory()->create([
                        'season_id' => $season->id,
                        'category_id' => $category->id,
                        'name' => 'Torneo ' . $category->name,
                    ]);

                    // Generar Roster para la Temporada (usando los equipos de la categoría)
                    foreach ($category->teams as $team) {
                        foreach ($team->players as $player) {
                            \Illuminate\Support\Facades\DB::table('team_player_season')->insertOrIgnore([
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
                }
            }
        }
    }
}