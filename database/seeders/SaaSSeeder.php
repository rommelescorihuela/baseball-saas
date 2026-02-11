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
                // Crear 2 Competencias por Temporada
                $competitions = Competition::factory()->count(2)->create([
                    'season_id' => $season->id,
                ]);

                foreach ($competitions as $competition) {
                    // Crear 4 Categorías por Competencia
                    $categories = Category::factory()->count(4)->create([
                        'competition_id' => $competition->id,
                    ]);

                    // Distribuir equipos en las categorías (3 equipos por categoría)
                    $shuffledTeams = $teams->shuffle();
                    $chunks = $shuffledTeams->split($categories->count());

                    foreach ($categories as $index => $category) {
                        $teamsInCat = $chunks->get($index);

                        if ($teamsInCat) {
                            // Inscribir equipos en la categoría
                            $category->teams()->attach($teamsInCat);

                            // Generar Roster para la Temporada
                            foreach ($teamsInCat as $team) {
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
    }
}