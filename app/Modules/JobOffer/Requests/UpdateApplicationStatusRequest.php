<?php

namespace App\Modules\JobOffer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,reviewed,accepted,rejected',
            'admin_notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être : en attente, examinée, acceptée ou refusée',
        ];
    }
}