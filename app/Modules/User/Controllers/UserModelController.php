<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\UserModel;
use App\Modules\User\Requests\UserModelRequest;
use App\Modules\User\Ressources\UserModelResource;

class UserModelController extends Controller
{
    public function index()
    {
        return response()->json(UserModelResource::collection(UserModel::all()));
    }

    public function store(UserModelRequest $request)
    {
        $user = UserModel::create($request->validated());
        $user->load('role');
        return response()->json(new UserModelResource($user));
    }

    public function show(UserModel $userModel)
    {
        $userModel->load('role');
        return response()->json(new UserModelResource($userModel));
    }

    public function update(UserModelRequest $request, UserModel $userModel)
    {
        $userModel->update($request->validated());
        $userModel->load('role');
        return response()->json(new UserModelResource($userModel));
    }

    public function destroy(UserModel $userModel)
    {
        $userModel->delete();

        return response()->json();
    }
}
