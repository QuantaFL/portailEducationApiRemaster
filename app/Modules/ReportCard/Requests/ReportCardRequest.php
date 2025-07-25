<?php

namespace App\Modules\ReportCard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'average_grade' => ['required'],
            'honors' => ['required'],
            'student_id' => ['required', 'exists:students'],
            'term_id' => ['required', 'exists:terms'],
            'path' => ['required'],
            'rank' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
