<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\League>
 */
class LeagueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company.' League';

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(8),
            'status' => 'active',
            'plan' => 'free',
            'subscription_status' => 'active',
        ];
    }
}
