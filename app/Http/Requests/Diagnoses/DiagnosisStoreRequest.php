<?php

namespace App\Http\Requests\Diagnoses;

use App\Concerns\PhotoUploadValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class DiagnosisStoreRequest extends FormRequest
{
    use PhotoUploadValidationRules;

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
            'photo' => $this->photoRules(),
            'plant_id' => ['nullable', 'integer', 'exists:plants,id'],
        ];
    }
}
