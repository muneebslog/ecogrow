<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\UserProgress;
use App\Services\Gamification\GamificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RewardsController extends Controller
{
    public function __construct(
        private readonly GamificationService $gamification,
    ) {}

    /**
     * Show XP/level progress, streak, impact stats, and badges.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $progress = UserProgress::forUser($user);

        $thresholds = $this->gamification->levelThresholds($progress->level);
        $earnedBadgeIds = $user->badges()->pluck('badges.id');

        return Inertia::render('rewards/index', [
            'progress' => [
                'level' => $progress->level,
                'level_name' => $this->gamification->levelName($progress->level),
                'xp' => $progress->xp,
                'xp_floor' => $thresholds['floor'],
                'xp_ceiling' => $thresholds['ceiling'],
                'current_streak_days' => $progress->current_streak_days,
                'longest_streak_days' => $progress->longest_streak_days,
                'co2_offset_kg' => (float) $progress->co2_offset_kg,
            ],
            'plantCount' => $user->plants()->count(),
            'badges' => Badge::query()->orderBy('criteria_threshold')->get()->map(fn (Badge $badge) => [
                'id' => $badge->id,
                'code' => $badge->code,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'earned' => $earnedBadgeIds->contains($badge->id),
            ]),
        ]);
    }
}
