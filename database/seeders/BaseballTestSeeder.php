<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use App\Models\Season;
use App\Models\Game;
use App\Models\PlayerStat;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Category;

class BaseballTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->command->info('Iniciando seeder de prueba...');

        // ================================
        // 1️⃣ Crear ligas
        // ================================
        $league1 = League::firstOrCreate(
            ['slug' => 'liga-central'],
            ['name' => 'Liga Central', 'level' => 'academia']
        );

        $league2 = League::firstOrCreate(
            ['slug' => 'liga-norte'],
            ['name' => 'Liga Norte', 'level' => 'menor']
        );

        // ================================
        // 2️⃣ Crear equipos y Categorías
        // ================================
        $teamsData = [
            ['league' => $league1, 'name' => 'Leones', 'category' => 'U15', 'subdomain' => 'leones'],
            ['league' => $league1, 'name' => 'Tigres', 'category' => 'U12', 'subdomain' => 'tigres'],
            ['league' => $league2, 'name' => 'Águilas', 'category' => 'U15', 'subdomain' => 'aguilas'],
            ['league' => $league2, 'name' => 'Búfalos', 'category' => 'U12', 'subdomain' => 'bufalos'],
        ];

        $teams = [];
        foreach ($teamsData as $t) {
            // Asegurarse de que la categoría existe para la liga
            $category = Category::firstOrCreate(
                ['league_id' => $t['league']->id, 'name' => $t['category']]
            );

            $team = Team::firstOrCreate(
                ['subdomain' => $t['subdomain']],
                [
                    'league_id' => $t['league']->id,
                    'name' => $t['name'],
                    'category_id' => $category->id
                ]
            );
            $teams[] = $team;

            // Crear usuario dueño del equipo
            $user = User::firstOrCreate(
                ['email' => strtolower($t['name']) . '@test.com'],
                [
                    'name' => $t['name'] . ' Owner',
                    'password' => Hash::make('password123'),
                    'league_id' => $t['league']->id,
                    'team_id' => $team->id,
                ]
            );
            $user->assignRole('team-owner');
        }

        // ================================
        // 3️⃣ Crear super admin
        // ================================
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
            ]
        );
        $superAdmin->assignRole('super-admin');

        // ================================
        // 4️⃣ Crear temporadas
        // ================================
        $season1 = Season::firstOrCreate(
            ['league_id' => $league1->id, 'name' => '2026'],
            ['start_date' => '2026-01-01', 'end_date' => '2026-06-30']
        );

        $season2 = Season::firstOrCreate(
            ['league_id' => $league2->id, 'name' => '2026'],
            ['start_date' => '2026-01-01', 'end_date' => '2026-06-30']
        );

        // ================================
        // 5️⃣ Crear jugadores por equipo
        // ================================
        $players = [];
        foreach ($teams as $team) {
            for ($i = 1; $i <= 5; $i++) {
                $player = Player::firstOrCreate(
                    [
                        'team_id' => $team->id,
                        'first_name' => 'Jugador' . $i,
                        'last_name' => $team->name
                    ],
                    [
                        'number' => $i,
                        'position' => ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF'][array_rand(range(0, 8))]
                    ]
                );
                $players[] = $player;
            }
        }

        // ================================
        // 6️⃣ Crear juegos y estadísticas
        // ================================
        $games = [];
        foreach ($teams as $team) {
            // Cada equipo juega contra otro equipo de la misma liga
            $opponent = $teams[array_rand(array_filter($teams, fn($x) => $x->league_id === $team->league_id && $x->id !== $team->id))];

            $game = Game::firstOrCreate(
                [
                    'season_id' => $team->league->id == $league1->id ? $season1->id : $season2->id,
                    'home_team_id' => $team->id,
                    'away_team_id' => $opponent->id,
                    'game_date' => Carbon::now()->addDays(rand(1, 10))
                ],
                [
                    'home_score' => rand(0, 10),
                    'away_score' => rand(0, 10)
                ]
            );
            $games[] = $game;

            // Estadísticas para cada jugador del equipo
            foreach ($team->players as $player) {
                PlayerStat::create([
                    'player_id' => $player->id,
                    'game_id' => $game->id,
                    'at_bats' => rand(1, 5),
                    'hits' => rand(0, 5),
                    'runs' => rand(0, 3),
                    'home_runs' => rand(0, 2),
                    'rbis' => rand(0, 4),
                    'walks' => rand(0, 2),
                    'strikeouts' => rand(0, 3),
                    'innings_pitched' => rand(0, 5),
                    'strikeouts_pitched' => rand(0, 5),
                    'runs_allowed' => rand(0, 5)
                ]);
            }
        }

        // ================================
        // 7️⃣ Crear Noticias (Articles)
        // ================================
        \App\Models\Article::create([
            'title' => 'Opening Day Approaches: Top 5 Prospects to Watch This Season',
            'slug' => 'opening-day-prospects',
            'content' => 'Full story about top prospects...',
            'category' => 'Highlight',
            'image_url' => '/images/hero-bg.png', // Dynamic data now uses the real image
            'published_at' => now(),
        ]);

        \App\Models\Article::create([
            'title' => 'Yankees maintain dominance in AL East standing',
            'slug' => 'yankees-dominance',
            'content' => 'Recap of the Yankees season so far...',
            'category' => 'Recap',
            'image_url' => 'https://placehold.co/600x400/0f172a/FFF?text=Recap',
            'published_at' => now()->subMinutes(45),
        ]);

        \App\Models\Article::create([
            'title' => 'Ohtanis historic run continues with 45th HR',
            'slug' => 'ohtani-45th-hr',
            'content' => 'Another home run for the history books...',
            'category' => 'Highlight',
            'published_at' => now()->subHours(2),
        ]);

        \App\Models\Article::create([
            'title' => 'Trade Deadline Winners and Losers',
            'slug' => 'trade-deadline-analysis',
            'content' => 'Analysis of the trade deadline moves...',
            'category' => 'Analysis',
            'published_at' => now()->subHours(5),
        ]);

        \App\Models\Article::create([
            'title' => 'Developing: Pitcher injury report update',
            'slug' => 'pitcher-injury-update',
            'content' => 'Breaking news on injuries...',
            'category' => 'Injury',
            'published_at' => now()->subHours(6),
        ]);

        $this->command->info('Seeder de prueba completado ✅');
    }
}
