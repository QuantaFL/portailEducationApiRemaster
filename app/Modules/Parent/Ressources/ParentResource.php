<?php

namespace App\Modules\Parent\Ressources;

use App\Modules\Parent\Models\ParentModel;
use App\Modules\User\Ressources\UserModelResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ParentModel */
class ParentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userModel' => new UserModelResource($this->whenLoaded('userModel')),
        ];
    }
}
