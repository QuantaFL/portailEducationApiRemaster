<?php

namespace App\Modules\Session\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SessionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label',
            'start_date' => ['required','digits:4'],
            'end_date' => ['required','digits:4'],
            'status'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
