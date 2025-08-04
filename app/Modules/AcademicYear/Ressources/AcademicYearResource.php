<?php

namespace App\Modules\AcademicYear\Ressources;

use App\Modules\AcademicYear\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AcademicYear */
class AcademicYearResource extends JsonResource
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
            'label' => $this->label,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
