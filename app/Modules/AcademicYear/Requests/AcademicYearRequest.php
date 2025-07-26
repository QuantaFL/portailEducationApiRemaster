<?php

namespace App\Modules\AcademicYear\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicYearRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label',
            'start_date' => ['required'],
            'end_date' => ['required'],
            'status' ,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
