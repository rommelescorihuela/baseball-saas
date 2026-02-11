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
            'home_team_id' => \App\Models\Team::factory(),
            'visitor_team_id' => \App\Models\Team::factory(),
            'start_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'location' => $this->faker->address,
            'status' => 'scheduled',
            'home_score' => 0,
            'visitor_score' => 0,
        ];
    }
}