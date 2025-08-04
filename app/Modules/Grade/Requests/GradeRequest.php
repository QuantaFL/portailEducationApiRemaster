<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GradeRequest
 *
 * Requête de formulaire pour les notes.
 */
class GradeRequest extends FormRequest
{
    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'mark' => ['required', 'numeric'],
            'assignement_id' => ['required', 'exists:assignments'],
            'student_session_id' => ['required', 'exists:student_sessions'],
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
