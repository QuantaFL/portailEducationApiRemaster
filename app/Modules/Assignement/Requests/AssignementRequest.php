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
            'academic_year_id' => ['required', 'exists:academic_years'],
            'day_of_week' => ['nullable', 'array', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
