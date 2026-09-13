<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Seed the badge catalog.
     *
     * Note: "Neighborhood Hero" is reinterpreted here as a CO2-impact badge
     * (criteria_type=impact_kg) rather than a social/community one, since
     * community features are out of scope for this slice. Easy to re-point
     * at social criteria once community features ship.
     */
    public function run(): void
    {
        $badges = [
            [
                'code' => 'first_tree_planted',
                'name' => 'First Tree Planted',
                'description' => 'Added your first plant to your garden.',
                'icon' => 'sprout',
                'criteria_type' => 'plant_count',
                'criteria_threshold' => 1,
            ],
            [
                'code' => 'thirty_day_streak',
                'name' => '30-Day Streak',
                'description' => 'Logged care 30 days in a row.',
                'icon' => 'flame',
                'criteria_type' => 'streak_days',
                'criteria_threshold' => 30,
            ],
            [
                'code' => 'master_planter',
                'name' => 'Master Planter',
                'description' => 'Grew a garden of 10 or more plants.',
                'icon' => 'medal',
                'criteria_type' => 'plant_count',
                'criteria_threshold' => 10,
            ],
            [
                'code' => 'neighborhood_hero',
                'name' => 'Neighborhood Hero',
                'description' => 'Offset 20kg of CO2 through your garden\'s growth.',
                'icon' => 'leaf',
                'criteria_type' => 'impact_kg',
                'criteria_threshold' => 20,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::query()->updateOrCreate(['code' => $badge['code']], $badge);
        }
    }
}
