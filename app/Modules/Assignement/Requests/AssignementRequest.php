<?php

namespace App\Modules\Assignement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_model_id' => ['required', 'exists:class_models,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'academic_year_id' => ['sometimes', 'exists:academic_years,id'],
            'day_of_week' => ['sometimes', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'coefficient' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
