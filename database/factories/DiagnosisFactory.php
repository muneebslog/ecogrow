<?php

namespace Database\Factories;

use App\Models\Diagnosis;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Deterministic test fixture data — no Faker.
 *
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
            'confidence' => 90,
            'health_status' => 'healthy',
            'findings' => [
                ['label' => 'Leaf discoloration', 'confidence' => 80, 'description' => 'Test fixture finding.'],
            ],
            'recommendation' => 'Test fixture recommendation.',
            'raw_response' => null,
        ];
    }
}
