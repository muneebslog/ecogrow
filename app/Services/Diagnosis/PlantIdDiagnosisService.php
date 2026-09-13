<?php

namespace App\Services\Diagnosis;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Calls the real Plant.id (Kindwise) v3 API.
 *
 * Confirmed directly against Kindwise's official Postman docs
 * (https://documenter.getpostman.com/view/24599534/2s93z5A4v2):
 * - Base URL is https://plant.id/api/v3 (NOT api.plant.id — an easy, load-bearing
 *   mistake, since the wrong host would fail every request).
 * - `Api-Key` header for auth; `images` in the JSON body accepts base64 strings
 *   or public URLs.
 * - The `health=all` query param on POST /identification folds health-assessment
 *   data into the SAME response as species classification (a dedicated
 *   /health_assessment endpoint also exists but isn't needed for this one-shot flow).
 * - result.classification.suggestions[] = {id, name, probability, details}.
 * - result.is_healthy.{binary, probability}; result.disease.suggestions[] =
 *   {id, name, probability, details}, where details.description and
 *   details.treatment.{biological,chemical,prevention} are documented as plain
 *   strings, not arrays/objects — parsing below matches that.
 * Still defensive (null-coalescing throughout) so an undocumented shape quirk
 * degrades to a missing field rather than a crash.
 */
class PlantIdDiagnosisService implements PlantDiagnosisService
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $baseUrl,
    ) {}

    public function diagnose(string $storedPhotoPath): DiagnosisResult
    {
        if (blank($this->apiKey)) {
            throw new DiagnosisUnavailableException(
                'Plant diagnosis is not configured — set PLANT_ID_API_KEY in .env.'
            );
        }

        if (! Storage::disk('public')->exists($storedPhotoPath)) {
            throw new DiagnosisUnavailableException("Stored photo not found: {$storedPhotoPath}");
        }

        $image = base64_encode(Storage::disk('public')->get($storedPhotoPath));

        $query = http_build_query([
            'details' => 'common_names,url,description',
            'health' => 'all',
        ]);

        $response = Http::withHeaders(['Api-Key' => $this->apiKey])
            ->timeout(30)
            ->post("{$this->baseUrl}/identification?{$query}", [
                'images' => [$image],
            ]);

        if ($response->failed()) {
            throw new DiagnosisUnavailableException(
                "Plant.id request failed with status {$response->status()}."
            );
        }

        return $this->fromApiResponse($response->json() ?? []);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fromApiResponse(array $data): DiagnosisResult
    {
        $result = $data['result'] ?? [];

        $topSuggestion = $result['classification']['suggestions'][0] ?? null;
        $scientificName = $topSuggestion['name'] ?? null;
        $commonNames = $topSuggestion['details']['common_names'] ?? [];
        $speciesName = $commonNames[0] ?? $scientificName;
        $confidence = isset($topSuggestion['probability'])
            ? (int) round($topSuggestion['probability'] * 100)
            : null;
        $isPlantConfidence = isset($result['is_plant']['probability'])
            ? (int) round($result['is_plant']['probability'] * 100)
            : null;

        $isHealthy = $result['is_healthy']['binary'] ?? null;
        $diseaseSuggestions = $result['disease']['suggestions'] ?? [];
        $topDisease = $diseaseSuggestions[0] ?? null;

        $healthStatus = match (true) {
            $isHealthy === true => 'healthy',
            $topDisease !== null && Str::contains(
                Str::lower($topDisease['name'] ?? ''),
                ['pest', 'insect', 'mite', 'aphid']
            ) => 'pest',
            $topDisease !== null => 'diseased',
            $isHealthy === false => 'stressed',
            default => 'unknown',
        };

        $findings = array_map(
            fn (array $suggestion): array => [
                'label' => $suggestion['name'] ?? 'Unknown finding',
                'confidence' => isset($suggestion['probability'])
                    ? (int) round($suggestion['probability'] * 100)
                    : 0,
                'description' => $suggestion['details']['description']
                    ?? $suggestion['details']['local_name']
                    ?? '',
            ],
            array_slice($diseaseSuggestions, 0, 3)
        );

        $recommendation = $topDisease['details']['treatment']['prevention']
            ?? $topDisease['details']['treatment']['biological']
            ?? null;

        return new DiagnosisResult(
            speciesName: $speciesName,
            scientificName: $scientificName,
            confidence: $confidence,
            isPlantConfidence: $isPlantConfidence,
            healthStatus: $healthStatus,
            findings: $findings,
            recommendation: $recommendation,
            raw: $data,
        );
    }
}
