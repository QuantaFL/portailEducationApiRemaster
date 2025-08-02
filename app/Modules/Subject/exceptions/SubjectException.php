<?php

namespace App\Modules\Subject\exceptions;

use Exception;
class SubjectException extends Exception
{
    public static function SubjectNameConflict(): self
    {
        return new self('une matière avec ce nom existe déjà ');
    }
}
