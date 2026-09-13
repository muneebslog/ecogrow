<?php

namespace App\Http\Requests\Plants;

use Illuminate\Foundation\Http\FormRequest;

class PlantStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'species_id' => ['required', 'integer', 'exists:species,id'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'planted_at' => ['nullable', 'date'],
        ];
    }
}
