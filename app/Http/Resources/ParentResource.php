<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Parent;

/** @mixin Parent */
class ParentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user_model_id' => $this->user_model_id,

            'userModel' => new UserModelResource($this->whenLoaded('userModel')),
        ];
    }
}
