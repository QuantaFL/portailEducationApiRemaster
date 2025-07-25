<?php

namespace App\Modules\ReportCard\Ressources;

use App\Modules\ReportCard\Models\ReportCard;
use App\Modules\Student\Resources\StudentResource;
use App\Modules\Term\Ressources\TermResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ReportCard */
class ReportCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'average_grade' => $this->average_grade,
            'honors' => $this->honors,
            'path' => $this->path,
            'rank' => $this->rank,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'student_session_id' => $this->student_session_id,
            'term_id' => $this->term_id,

            'student' => new StudentResource($this->whenLoaded('student')),
            'term' => new TermResource($this->whenLoaded('term')),
        ];
    }
}
