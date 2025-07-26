<?php

namespace App\Modules\ClassModel\Models;

use App\Modules\Student\Models\StudentSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;
    protected $with = ['latestStudentSession'];

    protected $fillable = [
        'name',
        'level',
    ];

    public function latestStudentSession()
    {
        return $this->hasOne(StudentSession::class)->latestOfMany();
    }
}
