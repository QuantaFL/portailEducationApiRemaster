<?php

namespace App\Modules\Term\Ressources;

use App\Modules\Session\Ressources\SessionResource;
use App\Modules\Term\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Term */
class TermResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'academic_year_id' => $this->academic_year_id,

            'session' => new SessionResource($this->whenLoaded('session')),
        ];
    }
}
