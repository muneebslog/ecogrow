<?php

namespace App\Models;

use Database\Factories\PlantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $user_id
 * @property int $species_id
 * @property string|null $nickname
 * @property string|null $location
 * @property string $status
 * @property Carbon|null $last_watered_at
 * @property string|null $photo_path
 * @property Carbon|null $planted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['species_id', 'nickname', 'location', 'planted_at', 'last_watered_at', 'status', 'photo_path'])]
class Plant extends Model
{
    /** @use HasFactory<PlantFactory> */
    use HasFactory;

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
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * @return HasMany<CareLog, $this>
     */
    public function careLogs(): HasMany
    {
        return $this->hasMany(CareLog::class);
    }

    /**
     * @return HasMany<Diagnosis, $this>
     */
    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
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
            'last_watered_at' => 'datetime',
            'planted_at' => 'date',
        ];
    }
}
