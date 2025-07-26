<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetGradesByTermRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'term_id' => 'required|exists:terms,id',
            'class_model_id' => 'required|exists:class_models,id',
            'subject_id' => 'required|exists:subjects,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
