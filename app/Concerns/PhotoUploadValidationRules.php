<?php

namespace App\Concerns;

trait PhotoUploadValidationRules
{
    /**
     * @return array<int, mixed>
     */
    protected function photoRules(): array
    {
        return ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'];
    }
}
