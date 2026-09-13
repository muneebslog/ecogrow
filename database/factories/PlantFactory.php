<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plant>
 */
class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'species_id' => Species::factory(),
            'nickname' => fake()->optional()->firstName(),
            'location' => fake()->randomElement(['Balcony', 'Yard', 'Indoor']),
            'status' => 'healthy',
            'last_watered_at' => fake()->dateTimeBetween('-5 days', 'now'),
            'photo_path' => null,
            'planted_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
