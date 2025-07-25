<?php

namespace App\Modules\Session\Models;



enum StatutsSessionEnum:string
{
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
    case INACTIVE = 'inactive';
}
