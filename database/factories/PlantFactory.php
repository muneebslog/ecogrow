<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Deterministic test fixture data — no Faker.
 *
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
            'nickname' => null,
            'location' => 'Balcony',
            'status' => 'healthy',
            'last_watered_at' => now()->subDays(2),
            'photo_path' => null,
            'planted_at' => now()->subMonths(2),
        ];
    }
}
