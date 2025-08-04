<?php

namespace App\Modules\JobOffer\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateApplicationStatusRequest
 *
 * Requête de formulaire pour la mise à jour du statut d'une candidature.
 */
class UpdateApplicationStatusRequest extends FormRequest
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
            'status' => 'required|in:pending,reviewed,accepted,rejected',
            'admin_notes' => 'nullable|string',
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
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être : en attente, examinée, acceptée ou refusée',
        ];
    }
}
