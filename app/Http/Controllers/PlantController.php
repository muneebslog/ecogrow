<?php

namespace App\Http\Controllers;

use App\Http\Requests\Plants\PlantStoreRequest;
use App\Models\Plant;
use App\Services\Gamification\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PlantController extends Controller
{
    public function __construct(
        private readonly GamificationService $gamification,
    ) {}

    /**
     * Add a plant to the authenticated user's garden.
     */
    public function store(PlantStoreRequest $request): RedirectResponse
    {
        $plant = $request->user()->plants()->create([
            'species_id' => $request->validated('species_id'),
            'nickname' => $request->validated('nickname'),
            'location' => $request->validated('location'),
            'planted_at' => $request->validated('planted_at') ?? now(),
            'last_watered_at' => now(),
        ]);

        $newBadges = $this->gamification->recordPlantAdded($request->user(), $plant);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $newBadges->isNotEmpty()
                ? "Plant added! New badge: {$newBadges->first()->name}"
                : 'Plant added to your garden.',
        ]);

        return to_route('dashboard');
    }

    /**
     * Show a plant's care plan.
     */
    public function show(Request $request, Plant $plant): Response
    {
        Gate::authorize('view', $plant);

        $plant->load(['species', 'careLogs' => fn ($query) => $query->latest('logged_at')->limit(10)]);

        return Inertia::render('plants/show', [
            'plant' => [
                'id' => $plant->id,
                'nickname' => $plant->nickname,
                'location' => $plant->location,
                'status' => $plant->status,
                'photo_url' => $plant->photo_url,
                'planted_at' => $plant->planted_at?->format('M j, Y'),
                'last_watered_at' => $plant->last_watered_at?->diffForHumans(),
                'species' => [
                    'name' => $plant->species->name,
                    'scientific_name' => $plant->species->scientific_name,
                    'sunlight' => $plant->species->sunlight,
                    'water_frequency_days' => $plant->species->water_frequency_days,
                    'care_difficulty' => $plant->species->care_difficulty,
                ],
            ],
            'careLogs' => $plant->careLogs->map(fn ($log) => [
                'id' => $log->id,
                'action' => $log->action,
                'notes' => $log->notes,
                'logged_at' => $log->logged_at->diffForHumans(),
            ]),
        ]);
    }
}
