<?php

namespace App\Http\Requests\CareLogs;

use Illuminate\Foundation\Http\FormRequest;

class CareLogStoreRequest extends FormRequest
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
            'action' => ['required', 'string', 'in:watered,fertilized,pruned,inspected,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
