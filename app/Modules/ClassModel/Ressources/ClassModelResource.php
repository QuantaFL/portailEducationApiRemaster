<?php

namespace App\Modules\ClassModel\Ressources;

use App\Modules\ClassModel\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ClassModel */
class ClassModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'latest_student_session' => $this->whenLoaded('latestStudentSession'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
