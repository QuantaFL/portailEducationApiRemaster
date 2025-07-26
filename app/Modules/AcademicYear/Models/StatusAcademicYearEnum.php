<?php

namespace App\Modules\AcademicYear\Models;

enum StatusAcademicYearEnum:string
{
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
    case INACTIVE = 'inactive';
}
