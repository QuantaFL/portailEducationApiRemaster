<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'matricule' => ['required'],
            'class_model_id' => ['required', 'exists:class_models'],
            'parent_id' => ['required', 'exists:parents'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
