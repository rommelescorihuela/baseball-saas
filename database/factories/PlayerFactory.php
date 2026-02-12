<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => \App\Models\Team::factory(),
            'league_id' => function (array $attributes) {
            return \App\Models\Team::find($attributes['team_id'])->league_id;
        },
            'name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'number' => $this->faker->numberBetween(1, 99),
            'date_of_birth' => $this->faker->date('Y-m-d', '-15 years'),
            'position' => $this->faker->randomElement(['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF']),
        ];
    }
}