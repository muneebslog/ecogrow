<?php

namespace Database\Factories;

use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Deterministic test fixture data — no Faker. Real seeded species data
 * lives in database/seeders/SpeciesSeeder.php; this factory only exists to
 * generate throwaway rows for the automated test suite.
 *
 * @extends Factory<Species>
 */
class SpeciesFactory extends Factory
{
    private static int $sequence = 0;

    /**
     * @var array<int, array{category: string, sunlight: string, difficulty: string}>
     */
    private const VARIANTS = [
        ['category' => 'tree', 'sunlight' => 'full_sun', 'difficulty' => 'easy'],
        ['category' => 'shrub', 'sunlight' => 'partial_shade', 'difficulty' => 'moderate'],
        ['category' => 'plant', 'sunlight' => 'low_light', 'difficulty' => 'easy'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $index = ++self::$sequence;
        $variant = self::VARIANTS[$index % count(self::VARIANTS)];
        $name = "Test Species {$index}";

        return [
            'name' => $name,
            'scientific_name' => "Testus speciesus {$index}",
            'slug' => "test-species-{$index}",
            'category' => $variant['category'],
            'description' => 'A test fixture species used only by the automated test suite.',
            'image_path' => null,
            'sunlight' => $variant['sunlight'],
            'water_frequency_days' => 3 + ($index % 5),
            'native_region' => 'Test Region',
            'co2_offset_kg_per_year' => 5.00,
            'care_difficulty' => $variant['difficulty'],
        ];
    }
}
