<?php

namespace App\Modules\JobOffer\Exceptions;

use Exception;

class JobOfferException extends Exception
{
    // Job Offer Exceptions
    public static function jobOfferNotFound(): self
    {
        return new self('Job offer not found');
    }

    public static function subjectNotFound(): self
    {
        return new self('Subject not found');
    }

    public static function userNotFound(): self
    {
        return new self('User not found');
    }

    public static function creationFailed(): self
    {
        return new self('Failed to create job offer');
    }

    public static function updateFailed(): self
    {
        return new self('Failed to update job offer');
    }

    public static function deletionFailed(): self
    {
        return new self('Failed to delete job offer');
    }

    public static function cannotDeleteWithApplications(): self
    {
        return new self('Cannot delete job offer with existing applications');
    }

    public static function invalidDeadline(): self
    {
        return new self('Application deadline must be in the future');
    }

    public static function invalidSalaryRange(): self
    {
        return new self('Minimum salary cannot be greater than maximum salary');
    }

    public static function invalidEmploymentType(): self
    {
        return new self('Invalid employment type. Allowed values: full_time, part_time, contract');
    }

    public static function invalidExperienceLevel(): self
    {
        return new self('Invalid experience level. Allowed values: entry, junior, senior, expert');
    }

    // Job Application Exceptions
    public static function applicationNotFound(): self
    {
        return new self('Job application not found');
    }

    public static function applicationCreationFailed(): self
    {
        return new self('Failed to create job application');
    }

    public static function applicationUpdateFailed(): self
    {
        return new self('Failed to update job application');
    }

    public static function applicationDeletionFailed(): self
    {
        return new self('Failed to delete job application');
    }

    public static function duplicateApplication(): self
    {
        return new self('An application from this email already exists for this job offer');
    }

    public static function jobOfferNotActive(): self
    {
        return new self('Job offer is not active');
    }

    public static function jobOfferExpired(): self
    {
        return new self('Job offer application deadline has passed');
    }

    public static function invalidApplicationStatus(): self
    {
        return new self('Invalid application status. Allowed values: pending, reviewed, accepted, rejected');
    }

    // File Upload Exceptions
    public static function cvFileRequired(): self
    {
        return new self('CV file is required');
    }

    public static function fileUploadFailed(): self
    {
        return new self('Failed to upload file');
    }

    public static function fileTooLarge(): self
    {
        return new self('File size exceeds maximum allowed size (5MB)');
    }

    public static function invalidFileType(): self
    {
        return new self('Invalid file type. Allowed types: PDF, DOC, DOCX');
    }

    public static function invalidFile(): self
    {
        return new self('Invalid or corrupted file');
    }

    public static function fileNotFound(): self
    {
        return new self('File not found');
    }
}