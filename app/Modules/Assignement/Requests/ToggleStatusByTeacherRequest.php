<?php

namespace App\Modules\Assignement\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ToggleStatusByTeacherRequest
 *
 * Requête de formulaire pour basculer le statut d'une affectation par un enseignant.
 */
class ToggleStatusByTeacherRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
        ];
    }

    /**
     * Récupère les messages de validation personnalisés.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'teacher_id.required' => 'L\'ID de l\'enseignant est obligatoire.',
            'teacher_id.integer' => 'L\'ID de l\'enseignant doit être un nombre entier.',
            'teacher_id.exists' => 'Cet enseignant n\'existe pas.',
        ];
    }
}
