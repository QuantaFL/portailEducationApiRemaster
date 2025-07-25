<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mark' => ['required', 'numeric'],
            'assignement_id' => ['required', 'exists:assignements'],
            'student_id' => ['required', 'exists:students'],
            'term_id' => ['required', 'exists:terms,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
