<?php

namespace App\Modules\AcademicYear\Models;

/**
 * Énumération pour le statut de l'année académique.
 */
enum StatusAcademicYearEnum:string
{
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
    case INACTIVE = 'inactive';
}
