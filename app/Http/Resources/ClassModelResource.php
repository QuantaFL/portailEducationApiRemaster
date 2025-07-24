<?php

namespace App\Http\Resources;

use App\Models\ClassModel;
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'session_id' => $this->session_id,

            'session' => new SessionResource($this->whenLoaded('session')),
        ];
    }
}
