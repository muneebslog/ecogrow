<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\QuizSubmitRequest;
use App\Services\Onboarding\SpeciesMatchService;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    public function __construct(
        private readonly SpeciesMatchService $matcher,
    ) {}

    /**
     * Show the species-match quiz.
     */
    public function show(): Response
    {
        return Inertia::render('onboarding/quiz');
    }

    /**
     * Score the species catalog against the submitted answers and show recommendations.
     */
    public function submit(QuizSubmitRequest $request): Response
    {
        $matches = $this->matcher->match(
            $request->validated('sunlight'),
            $request->validated('placement'),
        );

        return Inertia::render('onboarding/recommendations', [
            'sunlight' => $request->validated('sunlight'),
            'placement' => $request->validated('placement'),
            'matches' => $matches->map(fn (array $match) => [
                'species_id' => $match['species']->id,
                'name' => $match['species']->name,
                'scientific_name' => $match['species']->scientific_name,
                'category' => $match['species']->category,
                'care_difficulty' => $match['species']->care_difficulty,
                'native_region' => $match['species']->native_region,
                'match_percent' => $match['matchPercent'],
            ]),
        ]);
    }
}
