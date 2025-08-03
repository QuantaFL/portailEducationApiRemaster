<?php

namespace App\Modules\Assignement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleStatusByTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.required' => 'L\'ID de l\'enseignant est obligatoire.',
            'teacher_id.integer' => 'L\'ID de l\'enseignant doit être un nombre entier.',
            'teacher_id.exists' => 'Cet enseignant n\'existe pas.',
        ];
    }
}