<?php

namespace App\Services\Care;

use RuntimeException;

/**
 * Thrown when real weather data can't be produced — e.g. no location on file,
 * a city couldn't be geocoded, or the provider call failed.
 */
class WeatherUnavailableException extends RuntimeException {}
