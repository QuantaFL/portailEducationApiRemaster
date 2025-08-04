<?php

namespace App\Modules\AcademicYear\Exceptions;

use Exception;

/**
 * Class AcademicYearException
 *
 * Exception personnalisée pour les erreurs liées aux années académiques.
 */
class AcademicYearException extends Exception
{
    /**
     * @return self
     */
    public static function duplicateAcademicYear(): self
    {
        return new self('Une année académique avec cette période existe déjà.', 400);
    }

    /**
     * @return self
     */
    public static function startDateInThePast(): self
    {
        $currentYear = now()->year;
        return new self("L'année de début doit être supérieure ou égale à l'année en cours ({$currentYear}).", 400);
    }

    /**
     * @return self
     */
    public static function invalidDateRange(): self
    {
        return new self('L\'année de fin doit être supérieure à l\'année de début.', 400);
    }

    /**
     * @return self
     */
    public static function invalidDuration(): self
    {
        return new self('Une année académique doit durer exactement un an.', 400);
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
    public static function noCurrentAcademicYear(): self
    {
        return new self('Aucune année académique en cours trouvée', 404);
    }

    /**
     * @return self
     */
    public static function noActiveAcademicYears(): self
    {
        return new self('Aucune année académique active trouvée', 404);
    }

    /**
     * @return self
     */
    public static function termCreationFailed(): self
    {
        return new self('Échec de la création des semestres pour l\'année académique', 500);
    }
}
