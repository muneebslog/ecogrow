<?php

namespace Database\Factories;

use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Species>
 */
class SpeciesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'scientific_name' => fake()->words(2, true),
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(['tree', 'shrub', 'plant']),
            'description' => fake()->sentence(),
            'image_path' => null,
            'sunlight' => fake()->randomElement(['full_sun', 'partial_shade', 'low_light']),
            'water_frequency_days' => fake()->numberBetween(1, 7),
            'native_region' => fake()->country(),
            'co2_offset_kg_per_year' => fake()->randomFloat(2, 1, 25),
            'care_difficulty' => fake()->randomElement(['easy', 'moderate', 'difficult']),
        ];
    }
}
