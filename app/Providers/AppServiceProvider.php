<?php

namespace App\Providers;

use App\Services\Care\OpenMeteoWeatherService;
use App\Services\Care\WeatherService;
use App\Services\Diagnosis\PlantDiagnosisService;
use App\Services\Diagnosis\PlantIdDiagnosisService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlantDiagnosisService::class, fn () => new PlantIdDiagnosisService(
            apiKey: config('services.plant_id.key'),
            baseUrl: config('services.plant_id.base_url'),
        ));

        $this->app->bind(WeatherService::class, fn () => new OpenMeteoWeatherService(
            forecastUrl: config('services.open_meteo.forecast_url'),
            geocodingUrl: config('services.open_meteo.geocoding_url'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
