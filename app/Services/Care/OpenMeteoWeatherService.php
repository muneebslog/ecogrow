<?php

namespace App\Services\Care;

use App\Models\Plant;
use App\Models\UserLocation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;

/**
 * Calls the real, free, keyless Open-Meteo forecast + geocoding APIs
 * (confirmed against https://open-meteo.com/en/docs and
 * https://open-meteo.com/en/docs/geocoding-api). The weather data itself is
 * real; the "stress" scoring in stressForecast()/banner() is our own honest
 * heuristic (watering cadence vs. real precipitation), not a provider result —
 * it never claims to be a scientific stress model.
 */
class OpenMeteoWeatherService implements WeatherService
{
    public function __construct(
        private readonly string $forecastUrl,
        private readonly string $geocodingUrl,
    ) {}

    public function geocodeCity(string $city): array
    {
        $response = Http::timeout(15)->get($this->geocodingUrl, [
            'name' => $city,
            'count' => 1,
        ]);

        if ($response->failed()) {
            throw new WeatherUnavailableException('Location lookup failed. Please try again.');
        }

        $match = $response->json('results.0');

        if (! $match) {
            throw new WeatherUnavailableException("Couldn't find a location matching \"{$city}\".");
        }

        return [
            'latitude' => (float) $match['latitude'],
            'longitude' => (float) $match['longitude'],
            'resolved_name' => trim(($match['name'] ?? $city).(isset($match['country']) ? ", {$match['country']}" : '')),
        ];
    }

    public function banner(Plant $plant, UserLocation $location): WeatherBanner
    {
        $daily = $this->fetchDaily($location);
        $species = $plant->species;

        $daysSinceWatered = $plant->last_watered_at
            ? CarbonImmutable::parse($plant->last_watered_at)->diffInDays(CarbonImmutable::now(), absolute: true)
            : null;

        $dueSoon = $daysSinceWatered !== null && $daysSinceWatered >= max(0, $species->water_frequency_days - 1);
        $tomorrowRain = isset($daily[1]) ? $daily[1]['precipitation_sum'] : null;

        if ($dueSoon && $tomorrowRain !== null && $tomorrowRain >= 2.0) {
            return new WeatherBanner(
                level: 'info',
                message: "Rain expected tomorrow ({$tomorrowRain}mm) — we'll skip your watering reminder.",
                source: 'open-meteo',
            );
        }

        if ($dueSoon) {
            return new WeatherBanner(
                level: 'warning',
                message: 'No significant rain expected and watering is due soon.',
                source: 'open-meteo',
            );
        }

        return new WeatherBanner(
            level: 'info',
            message: 'Conditions look normal — following the regular watering schedule.',
            source: 'open-meteo',
        );
    }

    public function stressForecast(Plant $plant, UserLocation $location): array
    {
        $daily = $this->fetchDaily($location);
        $species = $plant->species;

        $effectiveThreshold = match ($species->care_difficulty) {
            'easy' => $species->water_frequency_days + 1,
            'difficult' => max(1, $species->water_frequency_days - 1),
            default => $species->water_frequency_days,
        };

        $lastWatered = $plant->last_watered_at ? CarbonImmutable::parse($plant->last_watered_at) : null;

        return array_map(
            function (array $day) use ($lastWatered, $effectiveThreshold): StressForecastDay {
                $date = CarbonImmutable::parse($day['date']);
                $daysSince = $lastWatered ? $lastWatered->diffInDays($date, absolute: true) : $effectiveThreshold;
                $rain = $day['precipitation_sum'];

                [$level, $reason] = match (true) {
                    $daysSince >= $effectiveThreshold && $rain >= 2.0 => [
                        'moderate',
                        "Watering overdue, but {$rain}mm of rain expected may help.",
                    ],
                    $daysSince >= $effectiveThreshold => [
                        'high',
                        'No rain expected and watering is overdue.',
                    ],
                    $daysSince >= $effectiveThreshold - 1 => [
                        'moderate',
                        'Watering due soon.',
                    ],
                    default => [
                        'low',
                        'On schedule.',
                    ],
                };

                return new StressForecastDay(
                    date: $date->toDateString(),
                    label: $date->format('D'),
                    stressLevel: $level,
                    reason: $reason,
                );
            },
            array_slice($daily, 0, 7)
        );
    }

    /**
     * @return array<int, array{date: string, precipitation_sum: float}>
     */
    private function fetchDaily(UserLocation $location): array
    {
        $response = Http::timeout(15)->get($this->forecastUrl, [
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'daily' => 'precipitation_sum,temperature_2m_max,temperature_2m_min',
            'forecast_days' => 7,
            'timezone' => 'auto',
        ]);

        if ($response->failed()) {
            throw new WeatherUnavailableException('Weather lookup failed. Please try again.');
        }

        $times = $response->json('daily.time') ?? [];
        $precipitation = $response->json('daily.precipitation_sum') ?? [];

        $daily = [];

        foreach ($times as $i => $date) {
            $daily[] = [
                'date' => $date,
                'precipitation_sum' => (float) ($precipitation[$i] ?? 0.0),
            ];
        }

        return $daily;
    }
}
