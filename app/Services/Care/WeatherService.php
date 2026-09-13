<?php

namespace App\Services\Care;

use App\Models\Plant;
use App\Models\UserLocation;

interface WeatherService
{
    /**
     * Resolve a free-text city to coordinates via geocoding.
     *
     * @return array{latitude: float, longitude: float, resolved_name: string}
     *
     * @throws WeatherUnavailableException When the city can't be resolved or the call fails.
     */
    public function geocodeCity(string $city): array;

    /**
     * A short, honest banner combining the plant's watering need with real
     * near-term precipitation at the user's location.
     *
     * @throws WeatherUnavailableException
     */
    public function banner(Plant $plant, UserLocation $location): WeatherBanner;

    /**
     * A 7-day stress forecast: real forecast data combined with the species'
     * watering cadence/difficulty. The weather input is real; the stress
     * scoring itself is our own heuristic, not a provider result.
     *
     * @return array<int, StressForecastDay>
     *
     * @throws WeatherUnavailableException
     */
    public function stressForecast(Plant $plant, UserLocation $location): array;
}
