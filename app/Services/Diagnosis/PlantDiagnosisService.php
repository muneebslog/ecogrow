<?php

namespace App\Services\Diagnosis;

interface PlantDiagnosisService
{
    /**
     * Diagnose a plant photo already stored on the "public" disk.
     *
     * @param  string  $storedPhotoPath  Path relative to the public disk, e.g. "diagnoses/3/abc123.jpg".
     *
     * @throws DiagnosisUnavailableException When the provider isn't configured or the call fails.
     */
    public function diagnose(string $storedPhotoPath): DiagnosisResult;
}
