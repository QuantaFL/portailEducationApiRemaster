<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateGradesRequest
 *
 * Requête de formulaire pour la mise à jour en masse des notes.
 */
class UpdateGradesRequest extends FormRequest
{
    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'grades' => 'required|array',
            'grades.*.student_session_id' => 'required|integer|exists:student_sessions,id',
            'grades.*.assignement_id' => 'required|integer|exists:assignments,id',
            'grades.*.type' => 'required|string',
            'grades.*.mark' => 'required|numeric|min:0|max:20',
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
