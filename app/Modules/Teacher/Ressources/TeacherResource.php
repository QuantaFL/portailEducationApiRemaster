<?php

namespace App\Modules\Teacher\Ressources;

use App\modules\teacher\models\Teacher;
use App\Modules\User\Ressources\UserModelResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Teacher */
class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'teacher_matricule' => $this->teacher_matricule,
            'hire_date' => $this->hire_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_model_id' => $this->user_model_id,
            'photo_url' => $this->photo_url,
            'cv_url' => $this->cv_url,
            'diplomas_url' => $this->diplomas_url,
            'subjects' => $this->whenLoaded('subjects', $this->subjects ?? []),
            'assigned_classes' => $this->whenLoaded('assignedClasses', $this->assignedClasses ?? []),
            'count' => Teacher::all()->count(),
            'userModel' => $this->whenLoaded('userModel', function() {
                return new UserModelResource($this->userModel);
            }),
            /*
             *             'assignments' => $this->assignments ? $this->assignments->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'assignment_number' => $assignment->assignment_number,
                    'isActive' => $assignment->isActive,
                    'subject' => $assignment->subject->name ?? null,
                    'class' => $assignment->classModel->name ?? null,
                    'day_of_week' => $assignment->day_of_week,
                    'start_time' => $assignment->start_time,
                    'end_time' => $assignment->end_time,
                    'coefficient' => $assignment->coefficient,
                ];
            }) : [],

             *
             * */
        ];
    }
}
