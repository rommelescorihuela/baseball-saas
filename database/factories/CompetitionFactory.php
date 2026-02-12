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
            'category_id' => \App\Models\Category::factory(),
            'league_id' => function (array $attributes) {
            return \App\Models\Category::find($attributes['category_id'])->league_id;
        },
            'name' => 'Competition ' . rand(1000, 9999),
            'status' => 'scheduled',
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
        ];
    }
}