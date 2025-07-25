<?php

namespace App\Modules\Term\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TermRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('terms')->where(function ($query) {
                    return $query->where('academic_year_id', $this->academic_year_id);
                }),
            ],
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
