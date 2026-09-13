<?php

namespace App\Http\Controllers;

use App\Http\Requests\CareLogs\CareLogStoreRequest;
use App\Models\Plant;
use App\Services\Gamification\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CareLogController extends Controller
{
    public function __construct(
        private readonly GamificationService $gamification,
    ) {}

    /**
     * Log a care action for a plant.
     */
    public function store(CareLogStoreRequest $request, Plant $plant): RedirectResponse
    {
        Gate::authorize('view', $plant);

        $careLog = $plant->careLogs()->create([
            'user_id' => $request->user()->id,
            'action' => $request->validated('action'),
            'notes' => $request->validated('notes'),
            'logged_at' => now(),
        ]);

        if ($careLog->action === 'watered') {
            $plant->update(['last_watered_at' => now()]);
        }

        $newBadges = $this->gamification->recordCareLog($request->user(), $careLog);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $newBadges->isNotEmpty()
                ? "Care logged! New badge: {$newBadges->first()->name}"
                : 'Care logged.',
        ]);

        return back();
    }
}
