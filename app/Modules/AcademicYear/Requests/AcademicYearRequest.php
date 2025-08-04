<?php

namespace App\Modules\AcademicYear\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AcademicYearRequest
 *
 * Requête de formulaire pour l'année académique.
 */
class AcademicYearRequest extends FormRequest
{
    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['required', 'integer', 'min:2000'],
            'end_date' => ['required', 'integer', 'min:2000'],
            'status' => ['sometimes', 'string', 'in:en_cours,termine,inactive'],
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
