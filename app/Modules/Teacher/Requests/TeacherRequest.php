<?php

namespace App\Modules\Teacher\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'hire_date' => ['required'],
            'role_id' => ['required', 'integer', 'exists:role_models,id'],
            'nationality' => ['nullable', 'string'],
            'user' => ['required', 'array'],
            'user.first_name' => ['required', 'string'],
            'user.last_name' => ['required', 'string'],
            'user.birthday' => ['required', 'date'],
            'user.email' => ['required', 'email'],
            'user.password' => ['required', 'string'],
            'user.adress' => ['required', 'string'],
            'user.phone' => ['required', 'string'],
            'user.gender'=>['required'],
            
            // Optional file uploads
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'diplomas' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            
            // Optional assignment data
            'assignment' => ['nullable', 'array'],
            'assignment.subject_id' => ['required_with:assignment', 'integer', 'exists:subjects,id'],
            'assignment.class_model_id' => ['required_with:assignment', 'integer', 'exists:class_models,id'],
            'assignment.day_of_week' => ['nullable', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'assignment.start_time' => ['nullable', 'date_format:H:i'],
            'assignment.end_time' => ['nullable', 'date_format:H:i', 'after:assignment.start_time'],
            'assignment.coefficient' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
