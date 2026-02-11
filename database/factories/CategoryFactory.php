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
            'competition_id' => \App\Models\Competition::factory(),
            'name' => $this->faker->randomElement(['Sub-10', 'Sub-12', 'Sub-15', 'Sub-18', 'Amateur', 'Pro']),
        ];
    }
}