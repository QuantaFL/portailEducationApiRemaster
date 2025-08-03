<?php

namespace App\Modules\Subject\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'exists:subjects,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la matière est obligatoire.',
            'name.string' => 'Le nom de la matière doit être une chaîne de caractères.',
            'name.exists' => 'Cette matière n\'existe pas.',
        ];
    }
}