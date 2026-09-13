<?php

namespace App\Services\Diagnosis;

/**
 * The normalized result of a plant diagnosis call, regardless of provider.
 */
final readonly class DiagnosisResult
{
    /**
     * @param  array<int, array{label: string, confidence: int, description: string}>  $findings
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public ?string $speciesName,
        public ?string $scientificName,
        public ?int $confidence,
        public string $healthStatus,
        public array $findings,
        public ?string $recommendation,
        public array $raw,
    ) {}
}
