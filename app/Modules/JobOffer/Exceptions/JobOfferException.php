<?php

namespace App
Modules\JobOffer\Exceptions;

use Exception;

/**
 * Class JobOfferException
 *
 * Exception personnalisée pour les erreurs liées aux offres d'emploi et aux candidatures.
 */
class JobOfferException extends Exception
{
    // Exceptions pour les offres d'emploi
    public static function jobOfferNotFound(): self
    {
        return new self('Offre d\'emploi non trouvée');
    }

    public static function subjectNotFound(): self
    {
        return new self('Matière non trouvée');
    }

    public static function userNotFound(): self
    {
        return new self('Utilisateur non trouvé');
    }

    public static function creationFailed(): self
    {
        return new self('Échec de la création de l\'offre d\'emploi');
    }

    public static function updateFailed(): self
    {
        return new self('Échec de la mise à jour de l\'offre d\'emploi');
    }

    public static function deletionFailed(): self
    {
        return new self('Échec de la suppression de l\'offre d\'emploi');
    }

    public static function cannotDeleteWithApplications(): self
    {
        return new self('Impossible de supprimer une offre d\'emploi avec des candidatures existantes');
    }

    public static function invalidDeadline(): self
    {
        return new self('La date limite de candidature doit être dans le futur');
    }

    public static function invalidSalaryRange(): self
    {
        return new self('Le salaire minimum ne peut pas être supérieur au salaire maximum');
    }

    public static function invalidEmploymentType(): self
    {
        return new self('Type d\'emploi invalide. Valeurs autorisées : temps_plein, temps_partiel, contrat');
    }

    public static function invalidExperienceLevel(): self
    {
        return new self('Niveau d\'expérience invalide. Valeurs autorisées : debutant, junior, senior, expert');
    }

    // Exceptions pour les candidatures
    public static function applicationNotFound(): self
    {
        return new self('Candidature non trouvée');
    }

    public static function applicationCreationFailed(): self
    {
        return new self('Échec de la création de la candidature');
    }

    public static function applicationUpdateFailed(): self
    {
        return new self('Échec de la mise à jour de la candidature');
    }

    public static function applicationDeletionFailed(): self
    {
        return new self('Échec de la suppression de la candidature');
    }

    public static function duplicateApplication(): self
    {
        return new self('Une candidature de cet email existe déjà pour cette offre d\'emploi');
    }

    public static function jobOfferNotActive(): self
    {
        return new self('L\'offre d\'emploi n\'est pas active');
    }

    public static function jobOfferExpired(): self
    {
        return new self('La date limite de candidature pour cette offre d\'emploi est dépassée');
    }

    public static function invalidApplicationStatus(): self
    {
        return new self('Statut de candidature invalide. Valeurs autorisées : pending, reviewed, accepted, rejected');
    }

    // Exceptions pour le téléchargement de fichiers
    public static function cvFileRequired(): self
    {
        return new self('Le fichier CV est requis');
    }

    public static function fileUploadFailed(): self
    {
        return new self('Échec du téléchargement du fichier');
    }

    public static function fileTooLarge(): self
    {
        return new self('La taille du fichier dépasse la taille maximale autorisée (5 Mo)');
    }

    public static function invalidFileType(): self
    {
        return new self('Type de fichier invalide. Types autorisés : PDF, DOC, DOCX');
    }

    public static function invalidFile(): self
    {
        return new self('Fichier invalide ou corrompu');
    }

    public static function fileNotFound(): self
    {
        return new self('Fichier non trouvé');
    }
}