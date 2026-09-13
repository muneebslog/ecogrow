<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpeciesSeeder extends Seeder
{
    /**
     * Seed the species reference catalog.
     */
    public function run(): void
    {
        $species = [
            [
                'name' => 'Neem',
                'scientific_name' => 'Azadirachta indica',
                'category' => 'tree',
                'description' => 'A fast-growing, drought-tolerant tree valued for its air-purifying and pest-repelling properties.',
                'sunlight' => 'full_sun',
                'water_frequency_days' => 5,
                'native_region' => 'South Asia',
                'co2_offset_kg_per_year' => 21.77,
                'care_difficulty' => 'easy',
            ],
            [
                'name' => 'Gulmohar',
                'scientific_name' => 'Delonix regia',
                'category' => 'tree',
                'description' => 'A striking flowering tree with a broad canopy, known for vivid red-orange blooms.',
                'sunlight' => 'full_sun',
                'water_frequency_days' => 4,
                'native_region' => 'Madagascar (naturalized in South Asia)',
                'co2_offset_kg_per_year' => 18.50,
                'care_difficulty' => 'moderate',
            ],
            [
                'name' => 'Money Plant',
                'scientific_name' => 'Epipremnum aureum',
                'category' => 'plant',
                'description' => 'A hardy trailing vine that tolerates low light and irregular watering.',
                'sunlight' => 'low_light',
                'water_frequency_days' => 7,
                'native_region' => 'Southeast Asia',
                'co2_offset_kg_per_year' => 2.10,
                'care_difficulty' => 'easy',
            ],
            [
                'name' => 'Hibiscus',
                'scientific_name' => 'Hibiscus rosa-sinensis',
                'category' => 'shrub',
                'description' => 'A flowering shrub that attracts pollinators and blooms best with regular watering.',
                'sunlight' => 'partial_shade',
                'water_frequency_days' => 3,
                'native_region' => 'East Asia',
                'co2_offset_kg_per_year' => 5.40,
                'care_difficulty' => 'moderate',
            ],
        ];

        foreach ($species as $entry) {
            Species::query()->updateOrCreate(
                ['slug' => Str::slug($entry['name'])],
                [...$entry, 'slug' => Str::slug($entry['name'])]
            );
        }
    }
}
