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
