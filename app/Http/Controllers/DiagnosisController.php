<?php

namespace App\Http\Controllers;

use App\Http\Requests\Diagnoses\DiagnosisStoreRequest;
use App\Models\Diagnosis;
use App\Models\Plant;
use App\Services\Diagnosis\DiagnosisUnavailableException;
use App\Services\Diagnosis\PlantDiagnosisService;
use App\Services\Gamification\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DiagnosisController extends Controller
{
    public function __construct(
        private readonly PlantDiagnosisService $diagnosisService,
        private readonly GamificationService $gamification,
    ) {}

    /**
     * Show the photo-upload screen.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('diagnoses/create', [
            'plants' => $request->user()->plants()->with('species')->get()->map(fn (Plant $plant) => [
                'id' => $plant->id,
                'name' => $plant->nickname ?: $plant->species->name,
            ]),
            'preselectedPlantId' => $request->integer('plant_id') ?: null,
        ]);
    }

    /**
     * Upload a photo and run diagnosis against it.
     */
    public function store(DiagnosisStoreRequest $request): RedirectResponse
    {
        $plantId = $request->validated('plant_id');

        if ($plantId) {
            $plant = Plant::query()->findOrFail($plantId);
            Gate::authorize('view', $plant);
        }

        $path = $request->file('photo')->store('diagnoses/'.$request->user()->id, 'public');

        if ($path === false) {
            return back()->withErrors(['photo' => 'The photo could not be uploaded. Please try again.']);
        }

        try {
            $result = $this->diagnosisService->diagnose($path);
        } catch (DiagnosisUnavailableException $exception) {
            Storage::disk('public')->delete($path);

            return back()->withErrors(['photo' => $exception->getMessage()]);
        }

        $diagnosis = $request->user()->diagnoses()->create([
            'plant_id' => $plantId,
            'photo_path' => $path,
            'predicted_species_id' => null,
            'confidence' => $result->confidence,
            'health_status' => $result->healthStatus,
            'findings' => $result->findings,
            'recommendation' => $result->recommendation,
            'raw_response' => $result->raw,
        ]);

        $newStatus = match ($result->healthStatus) {
            'healthy' => 'healthy',
            'stressed' => 'needs_attention',
            'diseased', 'pest' => 'critical',
            default => null,
        };

        if ($plantId && $newStatus !== null) {
            Plant::query()->whereKey($plantId)->update(['status' => $newStatus]);
        }

        $this->gamification->recordDiagnosis($request->user());

        return to_route('diagnoses.show', $diagnosis);
    }

    /**
     * Show a diagnosis result.
     */
    public function show(Request $request, Diagnosis $diagnosis): Response
    {
        Gate::authorize('view', $diagnosis);

        $diagnosis->load('plant.species');

        return Inertia::render('diagnoses/show', [
            'diagnosis' => [
                'id' => $diagnosis->id,
                'photo_url' => $diagnosis->photo_url,
                'confidence' => $diagnosis->confidence,
                'health_status' => $diagnosis->health_status,
                'findings' => $diagnosis->findings,
                'recommendation' => $diagnosis->recommendation,
                'plant' => $diagnosis->plant ? [
                    'id' => $diagnosis->plant->id,
                    'name' => $diagnosis->plant->nickname ?: $diagnosis->plant->species->name,
                ] : null,
            ],
        ]);
    }
}
