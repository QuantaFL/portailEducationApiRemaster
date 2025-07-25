<?php

namespace App\Modules\Student\Resources;

use App\Modules\ClassModel\Ressources\ClassModelResource;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Student */
class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'matricule' => $this->matricule,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent_model_id' => $this->parent_model_id,
            'user_model_id' => $this->user_model_id,

            "latest_student_session" => $this->whenLoaded('latestStudentSession'),
            'parentModel' => $this->whenLoaded('parentModel'),
            'userModel' => $this->whenLoaded('userModel'),
            'academic_records_url' => $this->academic_records_url,
        ];
    }
}
