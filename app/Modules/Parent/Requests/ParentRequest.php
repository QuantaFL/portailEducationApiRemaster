<?php

namespace App\Modules\Parent\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_model_id' => ['required', 'exists:user_models'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
