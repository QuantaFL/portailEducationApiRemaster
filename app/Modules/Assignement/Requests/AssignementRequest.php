<?php

namespace App\Modules\Assignement\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AssignementRequest
 *
 * Requête de formulaire pour les affectations.
 */
class AssignementRequest extends FormRequest
{
    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_model_id' => ['required', 'exists:class_models,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'academic_year_id' => ['sometimes', 'exists:academic_years,id'],
            'day_of_week' => ['sometimes', 'array'],
            'day_of_week.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'coefficient' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }

    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
