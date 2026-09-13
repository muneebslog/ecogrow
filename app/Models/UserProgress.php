<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $xp
 * @property int $level
 * @property int $current_streak_days
 * @property int $longest_streak_days
 * @property Carbon|null $last_care_logged_date
 * @property float $co2_offset_kg
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'xp', 'level', 'current_streak_days', 'longest_streak_days', 'last_care_logged_date', 'co2_offset_kg'])]
class UserProgress extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Find or lazily create the progress row for a user. Explicitly passes
     * the default values rather than relying on `firstOrCreate()`'s single-array
     * form — that form omits unset columns from the INSERT and leaves them as
     * database-level defaults, which Eloquent never hydrates back onto the
     * in-memory model, so a freshly created row would read as null attributes
     * here even though the database has 0/1.
     */
    public static function forUser(User $user): self
    {
        /** @var self $progress */
        $progress = self::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'xp' => 0,
                'level' => 1,
                'current_streak_days' => 0,
                'longest_streak_days' => 0,
                'co2_offset_kg' => 0,
            ],
        );

        return $progress;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_care_logged_date' => 'date',
            'co2_offset_kg' => 'decimal:2',
        ];
    }
}
