<?php

namespace App\Modules\Subject\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'level' => ['required'],
            'coefficient' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
