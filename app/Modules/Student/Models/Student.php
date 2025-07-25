<?php

namespace App\Modules\Student\Models;

use App\Modules\ClassModel\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'class_model_id',
        'parent_id',
    ];

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Parent::class);
    }
}
