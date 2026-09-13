<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Deterministic test fixture data — no Faker. Real seeded badges live in
 * database/seeders/BadgeSeeder.php.
 *
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{
    private static int $sequence = 0;

    /**
     * @var array<int, string>
     */
    private const CRITERIA_TYPES = ['plant_count', 'streak_days', 'care_log_count', 'impact_kg'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $index = ++self::$sequence;

        return [
            'code' => "test_badge_{$index}",
            'name' => "Test Badge {$index}",
            'description' => 'A test fixture badge used only by the automated test suite.',
            'icon' => 'award',
            'criteria_type' => self::CRITERIA_TYPES[$index % count(self::CRITERIA_TYPES)],
            'criteria_threshold' => 5,
        ];
    }
}
