<?php

namespace App\Modules\Student\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentSessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'class_model_id' => $this->class_model_id,
            'academic_year_id' => $this->academic_year_id,
            'student' => new StudentResource($this->whenLoaded('student')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'justificatif_url' => $this->justificatif_url,
        ];
    }
}
