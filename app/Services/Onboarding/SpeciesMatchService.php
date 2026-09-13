<?php

namespace App\Services\Onboarding;

use App\Models\Species;
use Illuminate\Support\Collection;

/**
 * Scores the species catalog against a user's onboarding quiz answers.
 * Simple, explainable heuristic (not ML) — sunlight match dominates,
 * placement suitability breaks ties.
 */
class SpeciesMatchService
{
    /**
     * @return Collection<int, array{species: Species, matchPercent: int}>
     */
    public function match(string $sunlight, string $placement): Collection
    {
        return Species::query()
            ->get()
            ->map(fn (Species $species) => [
                'species' => $species,
                'matchPercent' => $this->score($species, $sunlight, $placement),
            ])
            ->sortByDesc('matchPercent')
            ->values();
    }

    private function score(Species $species, string $sunlight, string $placement): int
    {
        $score = 40;

        if ($species->sunlight === $sunlight) {
            $score += 40;
        }

        $score += match (true) {
            $placement === 'indoor' && in_array($species->sunlight, ['low_light', 'partial_shade'], true) => 15,
            $placement === 'yard' && in_array($species->category, ['tree', 'shrub'], true) => 15,
            $placement === 'balcony' && in_array($species->category, ['plant', 'shrub'], true) => 15,
            default => 0,
        };

        if ($species->care_difficulty === 'easy') {
            $score += 5;
        }

        return min($score, 99);
    }
}
