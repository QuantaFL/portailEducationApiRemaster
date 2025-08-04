<?php

namespace App\Modules\JobOffer\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class JobOfferRequest
 *
 * Requête de formulaire pour les offres d'emploi.
 */
class JobOfferRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'required|in:temps_plein,temps_partiel,contrat',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'experience_level' => 'required|in:debutant,junior,senior,expert',
            'application_deadline' => 'required|date|after:today',
            'is_active' => 'boolean',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:14',
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
            'title.required' => 'Le titre de l\'offre est obligatoire',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'description.required' => 'La description de l\'offre est obligatoire',
            'requirements.required' => 'Les exigences sont obligatoires',
            'subject_id.required' => 'La matière est obligatoire',
            'subject_id.exists' => 'La matière sélectionnée n\'existe pas',
            'employment_type.required' => 'Le type d\'emploi est obligatoire',
            'employment_type.in' => 'Le type d\'emploi doit être : temps plein, temps partiel ou contrat',
            'salary_min.numeric' => 'Le salaire minimum doit être un nombre',
            'salary_min.min' => 'Le salaire minimum ne peut pas être négatif',
            'salary_max.numeric' => 'Le salaire maximum doit être un nombre',
            'salary_max.min' => 'Le salaire maximum ne peut pas être négatif',
            'salary_max.gte' => 'Le salaire maximum doit être supérieur ou égal au salaire minimum',
            'experience_level.required' => 'Le niveau d\'expérience est obligatoire',
            'experience_level.in' => 'Le niveau d\'expérience doit être : débutant, junior, senior ou expert',
            'application_deadline.required' => 'La date limite de candidature est obligatoire',
            'application_deadline.date' => 'La date limite doit être une date valide',
            'application_deadline.after' => 'La date limite doit être dans le futur',
            'contact_email.required' => 'L\'email de contact est obligatoire',
            'contact_email.email' => 'L\'email de contact doit être une adresse email valide',
            'contact_phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
        ];
    }
}
