<?php

namespace App\Services\Gamification;

use App\Models\Badge;
use App\Models\CareLog;
use App\Models\Plant;
use App\Models\User;
use App\Models\UserProgress;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Orchestrates XP, levels, streaks, CO2-offset accrual, and badge awarding.
 * Lives here rather than on the models since it's invoked from three
 * different controllers (plants, care logs, diagnoses) and touches several
 * tables at once.
 */
class GamificationService
{
    /**
     * Cumulative XP required to reach each level (index 0 => level 1).
     *
     * @var array<int, int>
     */
    private const LEVEL_THRESHOLDS = [0, 100, 250, 500, 1000, 2000, 4000];

    private const XP_PLANT_ADDED = 25;

    private const XP_DIAGNOSIS = 5;

    /**
     * @var array<string, int>
     */
    private const XP_FOR_CARE_ACTION = [
        'watered' => 10,
        'fertilized' => 10,
        'pruned' => 8,
        'inspected' => 5,
        'other' => 5,
    ];

    /**
     * @return Collection<int, Badge>
     */
    public function recordPlantAdded(User $user, Plant $plant): Collection
    {
        $progress = $this->progressFor($user);
        $this->addXp($progress, self::XP_PLANT_ADDED);

        return $this->checkBadges($user, $progress);
    }

    /**
     * @return Collection<int, Badge>
     */
    public function recordCareLog(User $user, CareLog $careLog): Collection
    {
        $progress = $this->progressFor($user);

        $this->addXp($progress, self::XP_FOR_CARE_ACTION[$careLog->action] ?? self::XP_FOR_CARE_ACTION['other']);
        $this->updateStreak($progress, CarbonImmutable::parse($careLog->logged_at));
        $this->accrueImpact($user, $progress, CarbonImmutable::parse($careLog->logged_at));

        return $this->checkBadges($user, $progress);
    }

    /**
     * @return Collection<int, Badge>
     */
    public function recordDiagnosis(User $user): Collection
    {
        $progress = $this->progressFor($user);
        $this->addXp($progress, self::XP_DIAGNOSIS);

        return $this->checkBadges($user, $progress);
    }

    private function progressFor(User $user): UserProgress
    {
        /** @var UserProgress $progress */
        $progress = UserProgress::query()->firstOrCreate(['user_id' => $user->id]);

        return $progress;
    }

    private function addXp(UserProgress $progress, int $amount): void
    {
        $progress->xp += $amount;
        $progress->level = $this->levelForXp($progress->xp);
        $progress->save();
    }

    private function levelForXp(int $xp): int
    {
        $level = 1;

        foreach (self::LEVEL_THRESHOLDS as $index => $threshold) {
            if ($xp >= $threshold) {
                $level = $index + 1;
            }
        }

        return $level;
    }

    private function updateStreak(UserProgress $progress, CarbonImmutable $loggedAt): void
    {
        $today = $loggedAt->startOfDay();
        $last = $progress->last_care_logged_date
            ? CarbonImmutable::parse($progress->last_care_logged_date)->startOfDay()
            : null;

        if ($last !== null && $last->equalTo($today)) {
            return; // Already logged today — no change to the streak.
        }

        $progress->current_streak_days = ($last !== null && $last->addDay()->equalTo($today))
            ? $progress->current_streak_days + 1
            : 1;

        $progress->longest_streak_days = max($progress->longest_streak_days, $progress->current_streak_days);
        $progress->last_care_logged_date = Carbon::instance($today);
        $progress->save();
    }

    private function accrueImpact(User $user, UserProgress $progress, CarbonImmutable $loggedAt): void
    {
        // Accrue once per unique calendar day of activity (any plant), not per
        // log row, so it can't be gamed by logging the same plant repeatedly.
        $alreadyLoggedToday = CareLog::query()
            ->where('user_id', $user->id)
            ->whereDate('logged_at', $loggedAt->toDateString())
            ->count() > 1;

        if ($alreadyLoggedToday) {
            return;
        }

        $dailyOffset = $user->plants()
            ->with('species')
            ->get()
            ->sum(fn (Plant $plant) => (float) ($plant->species->co2_offset_kg_per_year ?? 0) / 365);

        $progress->co2_offset_kg += round($dailyOffset, 2);
        $progress->save();
    }

    /**
     * @return Collection<int, Badge>
     */
    private function checkBadges(User $user, UserProgress $progress): Collection
    {
        $alreadyEarnedIds = $user->badges()->pluck('badges.id');

        $newlyEarned = Badge::query()
            ->whereNotIn('id', $alreadyEarnedIds)
            ->get()
            ->filter(fn (Badge $badge) => $this->meetsCriteria($user, $progress, $badge));

        foreach ($newlyEarned as $badge) {
            $user->badges()->attach($badge->id, ['earned_at' => now()]);
        }

        return $newlyEarned->values();
    }

    private function meetsCriteria(User $user, UserProgress $progress, Badge $badge): bool
    {
        if ($badge->criteria_threshold === null) {
            return false;
        }

        return match ($badge->criteria_type) {
            'plant_count' => $user->plants()->count() >= $badge->criteria_threshold,
            'streak_days' => $progress->longest_streak_days >= $badge->criteria_threshold,
            'care_log_count' => $user->careLogs()->count() >= $badge->criteria_threshold,
            'impact_kg' => (float) $progress->co2_offset_kg >= $badge->criteria_threshold,
            default => false,
        };
    }
}
