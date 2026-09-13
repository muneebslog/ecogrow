<?php

namespace App\Models;

use Database\Factories\SpeciesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $scientific_name
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property string|null $image_path
 * @property string $sunlight
 * @property int $water_frequency_days
 * @property string|null $native_region
 * @property float $co2_offset_kg_per_year
 * @property string $care_difficulty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'scientific_name',
    'slug',
    'category',
    'description',
    'image_path',
    'sunlight',
    'water_frequency_days',
    'native_region',
    'co2_offset_kg_per_year',
    'care_difficulty',
])]
class Species extends Model
{
    /** @use HasFactory<SpeciesFactory> */
    use HasFactory;

    /**
     * @return HasMany<Plant, $this>
     */
    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'water_frequency_days' => 'integer',
            'co2_offset_kg_per_year' => 'decimal:2',
        ];
    }
}
