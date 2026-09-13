<?php

namespace App\Models;

use Database\Factories\DiagnosisFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int|null $plant_id
 * @property int $user_id
 * @property string $photo_path
 * @property int|null $predicted_species_id
 * @property string|null $predicted_species_name
 * @property string|null $predicted_scientific_name
 * @property int|null $confidence
 * @property int|null $is_plant_confidence
 * @property string|null $health_status
 * @property array<int, array{label: string, confidence: int, description: string}>|null $findings
 * @property string|null $recommendation
 * @property array<string, mixed>|null $raw_response
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'plant_id',
    'user_id',
    'photo_path',
    'predicted_species_id',
    'predicted_species_name',
    'predicted_scientific_name',
    'confidence',
    'is_plant_confidence',
    'health_status',
    'findings',
    'recommendation',
    'raw_response',
])]
class Diagnosis extends Model
{
    /** @use HasFactory<DiagnosisFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Plant, $this>
     */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Species, $this>
     */
    public function predictedSpecies(): BelongsTo
    {
        return $this->belongsTo(Species::class, 'predicted_species_id');
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null
        );
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'findings' => 'array',
            'raw_response' => 'array',
        ];
    }
}
