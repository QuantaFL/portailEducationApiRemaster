<?php

namespace App\Modules\Teacher\Models;

use App\Modules\Subject\Models\Subject;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'hire_date',
        'user_model_id',
        'teacher_matricule',
    ];
    protected $with = ['userModel', 'subjects'];

    public function userModel()
    {
        return $this->belongsTo(UserModel::class);
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }

    public function assignments()
    {
        return $this->hasMany(\App\Modules\Assignement\Models\Assignement::class);
    }

    public function assignedClasses()
    {
        return $this->belongsToMany(
            \App\Modules\ClassModel\Models\ClassModel::class,
            'assignments', // Table pivot
            'teacher_id',  // Clé étrangère sur la table pivot (assignments) qui lie au modèle Teacher
            'class_model_id' // Clé étrangère sur la table pivot (assignments) qui lie au modèle ClassModel
        )->whereHas('terms.academicYear', function ($query) {
            $query->where('status', \App\Modules\AcademicYear\Models\StatusAcademicYearEnum::EN_COURS->value);
        });
    }

    /**
     * Generate teacher matricule automatically
     * Format: T-{total_teachers+1}_{YYYY}_{XXXX}
     */
    public static function generateMatricule(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;

            // Get total teachers count + 1
            $totalTeachers = self::count() + 1;

            // Get current year
            $year = date('Y');

            // Generate 4 random digits
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Format: T-{total_teachers+1}_{YYYY}_{XXXX}
            $matricule = "T-{$totalTeachers}_{$year}_{$randomNumber}";

            // Check if this matricule already exists
            $exists = self::where('teacher_matricule', $matricule)->exists();

            if (!$exists) {
                Log::info('Teacher: Generated unique teacher matricule', [
                    'matricule' => $matricule,
                    'attempts' => $attempt
                ]);
                return $matricule;
            }

            Log::debug('Teacher: Generated matricule already exists, retrying', [
                'matricule' => $matricule,
                'attempt' => $attempt
            ]);

        } while ($attempt < $maxAttempts);

        // If we couldn't generate a unique matricule after max attempts, use timestamp-based approach
        $timestamp = time();
        $totalTeachers = self::count() + 1;
        $year = date('Y');
        $matricule = "T-{$totalTeachers}_{$year}_" . substr($timestamp, -4);

        Log::warning('Teacher: Using timestamp-based matricule after max attempts', [
            'matricule' => $matricule,
            'max_attempts_reached' => $maxAttempts
        ]);

        return $matricule;
    }

    /**
     * Boot method to auto-generate matricule on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($teacher) {
            if (empty($teacher->teacher_matricule)) {
                $teacher->teacher_matricule = self::generateMatricule();
                Log::info('Teacher: Auto-generated matricule for new teacher', [
                    'matricule' => $teacher->teacher_matricule
                ]);
            }
        });
    }
}
