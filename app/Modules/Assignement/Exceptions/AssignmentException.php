<?php

namespace App
Modules\Assignement\Exceptions;

use Exception;

/**
 * Class AssignmentException
 *
 * Exception personnalisée pour les erreurs liées aux affectations.
 */
class AssignmentException extends Exception
{
    /**
     * @return self
     */
    public static function assignmentNotFound(): self
    {
        return new self('Affectation non trouvée', 404);
    }

    /**
     * @return self
     */
    public static function teacherNotFound(): self
    {
        return new self('Enseignant non trouvé', 404);
    }

    /**
     * @return self
     */
    public static function classNotFound(): self
    {
        return new self('Classe non trouvée', 404);
    }

    /**
     * @return self
     */
    public static function subjectNotFound(): self
    {
        return new self('Matière non trouvée', 404);
    }

    /**
     * @return self
     */
    public static function academicYearNotFound(): self
    {
        return new self('Année académique non trouvée', 404);
    }

    /**
     * @return self
     */
    public static function duplicateAssignment(): self
    {
        return new self('Une affectation existe déjà pour cette combinaison enseignant, classe et matière', 400);
    }

    /**
     * @return self
     */
    public static function invalidTimeSlot(): self
    {
        return new self('Créneau horaire invalide : l\'heure de fin doit être après l\'heure de début', 400);
    }

    /**
     * @return self
     */
    public static function conflictingSchedule(): self
    {
        return new self('Conflit d\'horaire : l\'enseignant ou la classe est déjà affecté à ce moment-là', 409);
    }

    /**
     * @return self
     */
    public static function creationFailed(): self
    {
        return new self('Échec de la création de l\'affectation', 500);
    }

    /**
     * @return self
     */
    public static function updateFailed(): self
    {
        return new self('Échec de la mise à jour de l\'affectation', 500);
    }

    /**
     * @return self
     */
    public static function deletionFailed(): self
    {
        return new self('Échec de la suppression de l\'affectation', 500);
    }
}