<?php

namespace App\Modules\ReportCard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateReportCardsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'class_model_id' => ['required', 'exists:class_models,id'],
            'term_id' => ['required', 'exists:terms,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
