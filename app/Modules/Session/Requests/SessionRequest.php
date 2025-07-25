<?php

namespace App\Modules\Session\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SessionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'status' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
