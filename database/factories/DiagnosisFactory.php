<?php

namespace Database\Factories;

use App\Models\Diagnosis;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diagnosis>
 */
class DiagnosisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plant_id' => null,
            'user_id' => User::factory(),
            'photo_path' => 'diagnoses/example.jpg',
            'predicted_species_id' => null,
            'confidence' => fake()->numberBetween(60, 99),
            'health_status' => fake()->randomElement(['healthy', 'stressed', 'diseased', 'pest', 'unknown']),
            'findings' => [
                ['label' => 'Leaf discoloration', 'confidence' => fake()->numberBetween(50, 95), 'description' => fake()->sentence()],
            ],
            'recommendation' => fake()->sentence(),
            'raw_response' => null,
        ];
    }
}
