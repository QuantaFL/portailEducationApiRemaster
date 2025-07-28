<?php

namespace App\Modules\Teacher\Ressources;

use App\modules\teacher\models\Teacher;
use App\Modules\User\Ressources\UserModelResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Teacher */
class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hire_date' => $this->hire_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_model_id' => $this->user_model_id,
            'subjects' => $this->whenLoaded('subjects'),
            'assigned_classes' => $this->whenLoaded('assignedClasses'),
            'count' => Teacher::all()->count(),
            'userModel' => new UserModelResource($this->userModel),
        ];
    }
}
