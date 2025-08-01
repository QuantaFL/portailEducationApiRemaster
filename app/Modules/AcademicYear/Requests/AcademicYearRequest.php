<?php

namespace App\Modules\AcademicYear\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicYearRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['required', 'integer', 'min:2000'],
            'end_date' => ['required', 'integer', 'min:2000'],
            'status' => ['sometimes', 'string', 'in:en_cours,termine,inactive'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
