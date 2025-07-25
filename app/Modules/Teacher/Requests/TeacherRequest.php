<?php

namespace App\Modules\Teacher\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'hire_date' => ['required'],
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
