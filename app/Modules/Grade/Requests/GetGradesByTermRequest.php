<?php

namespace App\Modules\Grade\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GetGradesByTermRequest
 *
 * Requête de formulaire pour récupérer les notes par semestre.
 */
class GetGradesByTermRequest extends FormRequest
{
    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'class_model_id' => 'required|exists:class_models,id',
            'subject_id' => 'required|exists:subjects,id',
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
