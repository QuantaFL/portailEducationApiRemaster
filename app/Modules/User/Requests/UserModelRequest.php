<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserModelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'birthday' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required'],
            'adress' => ['required'],
            'phone' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
