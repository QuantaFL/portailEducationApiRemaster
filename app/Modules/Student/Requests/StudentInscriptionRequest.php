<?php

namespace App\Modules\Student\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentInscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Parent
            'parent_first_name' => ['required', 'string'],
            'parent_last_name' => ['required', 'string'],
            'parent_email' => ['required', 'email', 'max:255'],
            'parent_password' => ['required', 'string', 'min:6'],
            'parent_phone' => ['required', 'string'],
            'parent_adress' => ['nullable', 'string'],
            'parent_birthday' => ['required', 'date'],
            'parent_gender' => ['required', 'string'],
            // Élève
            'student_first_name' => ['required', 'string'],
            'student_last_name' => ['required', 'string'],
            'student_email' => ['required', 'email', 'max:255'],
            'student_password' => ['required', 'string', 'min:6'],
            'student_phone' => ['required', 'string'],
            'student_adress' => ['nullable', 'string'],
            'student_matricule' => ['required', 'string'],
            'student_birthday' => ['required', 'date'],
            'student_gender' => ['required', 'string'],
            // Session
            'class_model_id' => ['required', 'exists:class_models,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
