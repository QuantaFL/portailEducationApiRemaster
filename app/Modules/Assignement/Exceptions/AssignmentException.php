<?php

namespace App\Modules\Assignement\Exceptions;

use Exception;

class AssignmentException extends Exception
{
    public static function assignmentNotFound(): self
    {
        return new self('Assignment not found', 404);
    }

    public static function teacherNotFound(): self
    {
        return new self('Teacher not found', 404);
    }

    public static function classNotFound(): self
    {
        return new self('Class not found', 404);
    }

    public static function subjectNotFound(): self
    {
        return new self('Subject not found', 404);
    }

    public static function academicYearNotFound(): self
    {
        return new self('Academic year not found', 404);
    }

    public static function duplicateAssignment(): self
    {
        return new self('Assignment already exists for this teacher, class, and subject combination', 400);
    }

    public static function invalidTimeSlot(): self
    {
        return new self('Invalid time slot: end time must be after start time', 400);
    }

    public static function conflictingSchedule(): self
    {
        return new self('Conflicting schedule: teacher or class is already assigned at this time', 409);
    }

    public static function creationFailed(): self
    {
        return new self('Failed to create assignment', 500);
    }

    public static function updateFailed(): self
    {
        return new self('Failed to update assignment', 500);
    }

    public static function deletionFailed(): self
    {
        return new self('Failed to delete assignment', 500);
    }
}