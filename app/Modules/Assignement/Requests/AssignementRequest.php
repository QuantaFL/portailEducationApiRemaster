<?php

namespace App\Modules\Assignement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'exists:teachers'],
            'class_model_id' => ['required', 'exists:class_models'],
            'subject_id' => ['required', 'exists:subjects'],
            'term_id' => ['required', 'exists:terms'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
