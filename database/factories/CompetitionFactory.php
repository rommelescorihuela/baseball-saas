<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => \App\Models\Season::factory(),
            'name' => $this->faker->word . ' Cup',
            'status' => 'scheduled',
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }
}