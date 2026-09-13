<?php

namespace App\Services\Care;

final readonly class WeatherBanner
{
    public function __construct(
        public string $level, // info|warning|critical
        public string $message,
        public string $source, // e.g. "open-meteo"
    ) {}
}
