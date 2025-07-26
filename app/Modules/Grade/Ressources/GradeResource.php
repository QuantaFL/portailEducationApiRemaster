<?php

namespace App\Modules\Grade\Ressources;

use App\Modules\Assignement\Ressources\AssignementResource;
use App\Modules\Grade\Models\Grade;
use App\Modules\Student\Resources\StudentResource;
use App\Modules\Term\Ressources\TermResource;
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
            'student_session_id' => $this->student_session_id,
            'term_id' => $this->term_id,

            'assignement' => new AssignementResource($this->whenLoaded('assignement')),
            'student_session' => $this->whenLoaded('studentSession'),
            'subject' => new StudentResource($this->whenLoaded('subject')),
            'term' => new TermResource($this->whenLoaded('term')),
        ];
    }
}
