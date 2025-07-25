<?php

namespace App\Modules\Student\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'matricule' => ['required'],
            'class_model_id' => ['required', 'exists:class_models'],
            'parent_id' => ['required', 'exists:parents'],
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
