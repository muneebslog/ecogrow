<?php

namespace Database\Factories;

use App\Models\CareLog;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Deterministic test fixture data — no Faker.
 *
 * @extends Factory<CareLog>
 */
class CareLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plant_id' => Plant::factory(),
            'user_id' => User::factory(),
            'action' => 'watered',
            'notes' => null,
            'logged_at' => now(),
        ];
    }
}
