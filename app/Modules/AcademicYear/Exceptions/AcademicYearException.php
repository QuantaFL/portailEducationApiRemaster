<?php

namespace App\Modules\AcademicYear\Exceptions;

use Exception;

class AcademicYearException extends Exception
{
    public static function duplicateAcademicYear(): self
    {
        return new self('Une session avec cette période existe déjà.', 400);
    }

    public static function startDateInThePast(): self
    {
        $currentYear = now()->year;
        return new self("L'année de début doit être supérieure ou égale à l'année en cours ({$currentYear}).", 400);
    }

    public static function invalidDateRange(): self
    {
        return new self("L'année de fin doit être supérieure à celle de début.", 400);
    }

    public static function invalidDuration(): self
    {
        return new self('Une année académique doit durer exactement 1 an.', 400);
    }

    public static function academicYearNotFound(): self
    {
        return new self('Academic year not found', 404);
    }

    public static function noCurrentAcademicYear(): self
    {
        return new self('No current academic year found', 404);
    }

    public static function noActiveAcademicYears(): self
    {
        return new self('No active academic years found', 404);
    }

    public static function termCreationFailed(): self
    {
        return new self('Failed to create terms for academic year', 500);
    }
}