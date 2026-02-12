<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => \App\Models\Category::factory(),
            'league_id' => function (array $attributes) {
            return \App\Models\Category::find($attributes['category_id'])->league_id;
        },
            'competition_id' => \App\Models\Competition::factory(),
            'home_team_id' => \App\Models\Team::factory(),
            'visitor_team_id' => \App\Models\Team::factory(),
            'start_time' => now()->addDays(rand(1, 30)),
            'location' => 'Test Location ' . rand(1, 100),
            'status' => 'scheduled',
            'home_score' => 0,
            'visitor_score' => 0,
        ];
    }
}