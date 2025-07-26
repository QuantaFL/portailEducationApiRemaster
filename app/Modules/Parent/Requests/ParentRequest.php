<?php

namespace App\Modules\Parent\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
           // 'user_model_id' => ['required', 'exists:user_models'],
            'user' => ['required', 'array'],
            'user.first_name' => ['required', 'string'],
            'user.last_name' => ['required', 'string'],
            'user.birthday' => ['required', 'date'],
            'user.email' => ['required', 'email'],
            'user.password' => ['required', 'string'],
            'user.adress' => ['required', 'string'],
            'user.phone' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
