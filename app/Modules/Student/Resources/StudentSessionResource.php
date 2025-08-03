<?php

namespace App\Modules\Student\Resources;

use App\Modules\ClassModel\Ressources\ClassModelResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'academic_year_id' => $this->academic_year_id,
            'class_model_id' => $this->class_model_id,
            'class_model' => new ClassModelResource($this->whenLoaded('classModel')),
            'academic_year' => $this->whenLoaded('academicYear'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}