<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mark' => ['required', 'numeric'],
            'assignement_id' => ['required', 'exists:assignments'],
            'student_session_id' => ['required', 'exists:student_sessions'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
