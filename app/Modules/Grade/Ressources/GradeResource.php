<?php

namespace App\Modules\Grade\Ressources;

use App\Http\Resources\StudentResource;
use App\Modules\Assignement\Ressources\AssignementResource;
use App\Modules\Grade\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Grade */
class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mark' => $this->mark,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'assignement_id' => $this->assignement_id,
            'student_id' => $this->student_id,

            'assignement' => new AssignementResource($this->whenLoaded('assignement')),
            'student' => new StudentResource($this->whenLoaded('student')),
        ];
    }
}
