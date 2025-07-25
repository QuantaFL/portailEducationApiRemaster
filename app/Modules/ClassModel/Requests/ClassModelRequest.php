<?php

namespace App\Modules\ClassModel\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassModelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'level' => ['required'],
            'session_id' => ['required', 'exists:sessions'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
