<?php

namespace App\Modules\Student\Exceptions;

use Exception;

class StudentInscriptionException extends Exception
{
    public static function userCreationFailed(string $userType): self
    {
        return new self("Failed to create {$userType} user", 500);
    }

    public static function parentCreationFailed(): self
    {
        return new self('Failed to create parent model', 500);
    }

    public static function studentCreationFailed(): self
    {
        return new self('Failed to create student', 500);
    }

    public static function studentSessionCreationFailed(): self
    {
        return new self('Failed to create student session', 500);
    }

    public static function fileUploadFailed(): self
    {
        return new self('Failed to upload academic records file', 500);
    }

    public static function emailSendingFailed(): self
    {
        return new self('Failed to send welcome email', 500);
    }

    public static function inscriptionProcessFailed(): self
    {
        return new self('Student inscription process failed', 500);
    }

    public static function invalidClassOrAcademicYear(): self
    {
        return new self('Invalid class or academic year provided', 400);
    }

    public static function duplicateStudentMatricule(): self
    {
        return new self('Failed to generate unique student matricule', 500);
    }
}