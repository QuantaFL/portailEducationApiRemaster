<?php

namespace App\Modules\ClassModel\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ClassModelRequest
 *
 * Requête de formulaire pour les classes.
 */
class ClassModelRequest extends FormRequest
{
    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'level' => ['required'],
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
