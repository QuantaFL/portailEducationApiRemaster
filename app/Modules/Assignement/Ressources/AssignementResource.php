<?php

namespace App\Modules\Assignement\Ressources;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use App\Modules\AcademicYear\Ressources\AcademicYearResource;
use App\Modules\Subject\Ressources\SubjectResource;
use App\Modules\Teacher\Ressources\TeacherResource;
use App\Modules\Term\Ressources\TermResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Assignement */
class AssignementResource extends JsonResource
{
    /**
     * Transforme la ressource en un tableau.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'teacher_id' => $this->teacher_id,
            'class_model_id' => $this->class_model_id,
            'subject_id' => $this->subject_id,
            'term_id' => $this->term_id ?? null,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'classModel' => new ClassModelResource($this->whenLoaded('classModel')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'term' => new TermResource($this->whenLoaded('term')),
        ];
    }
}
