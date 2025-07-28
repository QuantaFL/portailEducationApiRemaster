<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'grades' => 'required|array',
            'grades.*.student_session_id' => 'required|integer|exists:student_sessions,id',
            'grades.*.assignement_id' => 'required|integer|exists:assignments,id',
            'grades.*.type' => 'required|string',
            'grades.*.mark' => 'required|numeric|min:0|max:20',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
