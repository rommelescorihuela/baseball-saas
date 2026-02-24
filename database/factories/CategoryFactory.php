<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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
            'name' => ['Sub-10', 'Sub-12', 'Sub-15', 'Sub-18', 'Amateur', 'Pro'][rand(0, 5)],
        ];
    }
}