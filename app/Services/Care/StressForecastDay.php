<?php

namespace App\Services\Care;

final readonly class StressForecastDay
{
    public function __construct(
        public string $date, // Y-m-d
        public string $label, // e.g. "Mon"
        public string $stressLevel, // low|moderate|high
        public string $reason,
    ) {}
}
