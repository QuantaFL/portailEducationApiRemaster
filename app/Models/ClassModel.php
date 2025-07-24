<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'session_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
