<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\Species;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the user's garden dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $plants = $user->plants()
            ->with('species')
            ->latest()
            ->get()
            ->map(fn ($plant) => [
                'id' => $plant->id,
                'nickname' => $plant->nickname,
                'species_name' => $plant->species->name,
                'status' => $plant->status,
                'needs_water' => $this->needsWater($plant),
                'last_watered_at' => $plant->last_watered_at?->diffForHumans(),
                'photo_url' => $plant->photo_url,
            ]);

        $progress = UserProgress::forUser($user);

        return Inertia::render('dashboard', [
            'plants' => $plants,
            'species' => Species::query()->orderBy('name')->get(['id', 'name', 'scientific_name']),
            'progress' => [
                'xp' => $progress->xp,
                'level' => $progress->level,
                'current_streak_days' => $progress->current_streak_days,
                'co2_offset_kg' => (float) $progress->co2_offset_kg,
            ],
            'attentionCount' => $plants->filter(fn (array $plant) => $plant['needs_water'] || $plant['status'] !== 'healthy')->count(),
        ]);
    }

    private function needsWater(Plant $plant): bool
    {
        if (! $plant->last_watered_at) {
            return true;
        }

        return $plant->last_watered_at->diffInDays(now()) >= $plant->species->water_frequency_days;
    }
}
