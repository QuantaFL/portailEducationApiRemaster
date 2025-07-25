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
        return response()->json(new UserModelResource(UserModel::create($request->validated())));
    }

    public function show(UserModel $userModel)
    {
        return response()->json(new UserModelResource($userModel));
    }

    public function update(UserModelRequest $request, UserModel $userModel)
    {
        $userModel->update($request->validated());

        return response()->json(new UserModelResource($userModel));
    }

    public function destroy(UserModel $userModel)
    {
        $userModel->delete();

        return response()->json();
    }
}
