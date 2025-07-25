<?php

namespace App\Modules\User\Ressources;

use App\Modules\User\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserModel */
class UserModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birthday' => $this->birthday,
            'email' => $this->email,
            'password' => $this->password,
            'adress' => $this->adress,
            'phone' => $this->phone,
            'role_id' => $this->role_id,
            'role' => $this->role ? [
                'id' => $this->role->id,
                'name' => $this->role->name,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'isFirstLogin' => $this->isFirstLogin === null ? true : (bool)$this->isFirstLogin,
        ];
    }
}
