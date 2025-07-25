<?php

namespace App\Modules\Term\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'session_id' => ['required', 'exists:sessions'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
