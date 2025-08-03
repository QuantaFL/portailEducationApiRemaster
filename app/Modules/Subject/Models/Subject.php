<?php

namespace App\Modules\Subject\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'coefficient',
        'status',
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Relation many-to-many avec ClassModel
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(\App\Modules\ClassModel\Models\ClassModel::class, 'class_subject', 'subject_id', 'class_model_id')
                    ->withTimestamps();
    }
}