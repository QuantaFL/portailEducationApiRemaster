<?php

namespace App\Modules\Session\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'status',
    ];
}
