<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'grades' => 'required|array',
            'grades.*.student_session_id' => 'required|exists:student_sessions,id',
            'grades.*.term_id' => 'required|exists:terms,id',
            'grades.*.assignement_id' => 'required|exists:assignments,id',
            'grades.*.mark' => 'required|numeric|min:0|max:20',
            'grades.*.type' => 'required|in:quiz,exam',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
