<?php

namespace App\Modules\JobOffer\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class JobApplicationRequest
 *
 * Requête de formulaire pour les candidatures.
 */
class JobApplicationRequest extends FormRequest
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
            'job_offer_id' => 'required|exists:job_offers,id',
            'applicant_first_name' => 'required|string|max:255',
            'applicant_last_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'nullable|string|max:20',
            'cover_letter' => 'nullable|string',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'cover_letter_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
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
            'job_offer_id.required' => 'L\'offre d\'emploi est obligatoire',
            'job_offer_id.exists' => 'L\'offre d\'emploi sélectionnée n\'existe pas',
            'applicant_first_name.required' => 'Le prénom est obligatoire',
            'applicant_first_name.max' => 'Le prénom ne peut pas dépasser 255 caractères',
            'applicant_last_name.required' => 'Le nom de famille est obligatoire',
            'applicant_last_name.max' => 'Le nom de famille ne peut pas dépasser 255 caractères',
            'applicant_email.required' => 'L\'adresse email est obligatoire',
            'applicant_email.email' => 'L\'adresse email doit être valide',
            'applicant_email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères',
            'applicant_phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
            'cv_file.required' => 'Le CV est obligatoire',
            'cv_file.file' => 'Le CV doit être un fichier',
            'cv_file.mimes' => 'Le CV doit être au format PDF, DOC ou DOCX',
            'cv_file.max' => 'Le CV ne peut pas dépasser 5 Mo',
            'cover_letter_file.file' => 'La lettre de motivation doit être un fichier',
            'cover_letter_file.mimes' => 'La lettre de motivation doit être au format PDF, DOC ou DOCX',
            'cover_letter_file.max' => 'La lettre de motivation ne peut pas dépasser 5 Mo',
        ];
    }
}
