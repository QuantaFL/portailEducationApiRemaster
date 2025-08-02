<?php

namespace App\Modules\Subject\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'level' => ['nullable'],
            'coefficient' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
