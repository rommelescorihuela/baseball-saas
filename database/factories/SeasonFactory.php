<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'league_id' => \App\Models\League::factory(),
            'name' => 'Temporada ' . rand(2025, 2030),
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ];
    }
}