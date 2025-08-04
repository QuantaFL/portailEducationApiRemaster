<?php

namespace App\Modules\Statistique\Exceptions;

use Exception;

class StatistiqueException extends Exception
{
    public static function dataNotFound(): self
    {
        return new self('Statistical data not found');
    }

    public static function calculationError(): self
    {
        return new self('Error occurred during statistical calculation');
    }

    public static function invalidAcademicYear(): self
    {
        return new self('Invalid academic year provided');
    }

    public static function assignmentNotFound(): self
    {
        return new self('Assignment not found');
    }

    public static function insufficientData(): self
    {
        return new self('Insufficient data to generate statistics');
    }
}