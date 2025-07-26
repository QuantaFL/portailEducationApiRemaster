<?php

namespace App\Modules\Assignement\Ressources;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use App\Modules\AcademicYear\Ressources\AcademicYearResource;
use App\Modules\Subject\Ressources\SubjectResource;
use App\modules\teacher\ressources\TeacherResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Assignement */
class AssignementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'teacher_id' => $this->teacher_id,
            'class_model_id' => $this->class_model_id,
            'subject_id' => $this->subject_id,
            'term_id' => $this->term_id,

            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'classModel' => new ClassModelResource($this->whenLoaded('classModel')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'term' => new AcademicYearResource($this->whenLoaded('term')),
        ];
    }
}
