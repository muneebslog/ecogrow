<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class QuizSubmitRequest extends FormRequest
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
            'sunlight' => ['required', 'string', 'in:full_sun,partial_shade,low_light'],
            'placement' => ['required', 'string', 'in:balcony,yard,indoor'],
        ];
    }
}
