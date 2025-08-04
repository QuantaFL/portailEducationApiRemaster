<?php

namespace App\Modules\ClassModel\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AssignSubjectRequest
 *
 * Requête de formulaire pour affecter une matière à une classe.
 */
class AssignSubjectRequest extends FormRequest
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
            'class_id' => ['required', 'integer', 'exists:class_models,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
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
            'class_id.required' => 'L\'ID de la classe est obligatoire.',
            'class_id.integer' => 'L\'ID de la classe doit être un nombre entier.',
            'class_id.exists' => 'Cette classe n\'existe pas.',
            'subject_id.required' => 'L\'ID de la matière est obligatoire.',
            'subject_id.integer' => 'L\'ID de la matière doit être un nombre entier.',
            'subject_id.exists' => 'Cette matière n\'existe pas.',
        ];
    }
}
