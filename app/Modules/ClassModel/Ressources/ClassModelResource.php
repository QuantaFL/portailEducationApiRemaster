<?php

namespace App\Modules\ClassModel\Ressources;

use App\Modules\ClassModel\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ClassModel */
class ClassModelResource extends JsonResource
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
            'name' => $this->name,
            'level' => $this->level,
            'current_academic_year_student_sessions' => $this->whenLoaded('currentAcademicYearStudentSessions'),
            'subjects' => $this->whenLoaded('subjects'),
            'count' => ClassModel::all()->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
