<?php

namespace App\Modules\Teacher\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'hire_date' => ['required'],
            'role_id' => ['required', 'integer', 'exists:role_models,id'],
            'nationality' => ['nullable', 'string'],
            'user' => ['required', 'array'],
            'user.first_name' => ['required', 'string'],
            'user.last_name' => ['required', 'string'],
            'user.birthday' => ['required', 'date'],
            'user.email' => ['required', 'email'],
            'user.password' => ['required', 'string'],
            'user.adress' => ['required', 'string'],
            'user.phone' => ['required', 'string'],
            'user.gender'=>['required']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
