<?php

namespace App\Modules\Teacher\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'hire_date' => ['required'],
            'user_model_id' => ['required', 'exists:user_models'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
