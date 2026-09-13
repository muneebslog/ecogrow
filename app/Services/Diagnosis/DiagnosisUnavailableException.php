<?php

namespace App\Services\Diagnosis;

use RuntimeException;

/**
 * Thrown when a real diagnosis can't be produced — e.g. no API key configured,
 * or the provider call failed. Callers must surface this to the user honestly
 * rather than falling back to a fabricated result.
 */
class DiagnosisUnavailableException extends RuntimeException {}
