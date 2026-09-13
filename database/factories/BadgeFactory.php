<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
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
            'code' => Str::slug($name, '_'),
            'name' => Str::title($name),
            'description' => fake()->sentence(),
            'icon' => 'award',
            'criteria_type' => fake()->randomElement(['plant_count', 'streak_days', 'care_log_count', 'impact_kg']),
            'criteria_threshold' => fake()->numberBetween(1, 30),
        ];
    }
}
