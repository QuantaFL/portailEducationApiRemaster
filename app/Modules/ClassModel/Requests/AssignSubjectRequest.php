<?php

namespace App\Modules\ClassModel\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => ['required', 'integer', 'exists:class_models,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'L\'ID de la classe est obligatoire.',
            'class_id.integer' => 'L\'ID de la classe doit être un nombre entier.',
            'class_id.exists' => 'Cette classe n\'existe pas.',
            'subject_id.required' => 'L\'ID de la matière est obligatoire.',
            'subject_id.integer' => 'L\'ID de la matière doit être un nombre entier.',
            'subject_id.exists' => 'Cette matière n\'existe pas.',
        ];
    }
}