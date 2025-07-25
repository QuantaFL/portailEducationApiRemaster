<?php

namespace App\Modules\Term\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'academic_year_id' => ['required', 'exists:academic_years'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
