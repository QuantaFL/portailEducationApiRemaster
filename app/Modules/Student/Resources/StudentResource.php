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

            'class_model_id' => $this->class_model_id,
            'parent_id' => $this->parent_id,
            'user_model_id' => $this->user_model_id,

            'classModel' => new ClassModelResource($this->whenLoaded('classModel')),
            'parent' => $this->whenLoaded('parent'),
            'userModel' => $this->whenLoaded('userModel'),
        ];
    }
}
