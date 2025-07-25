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
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
