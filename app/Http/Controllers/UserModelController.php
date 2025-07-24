<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserModelRequest;
use App\Http\Resources\UserModelResource;
use App\Models\UserModel;

class UserModelController extends Controller
{
    public function index()
    {
        return UserModelResource::collection(UserModel::all());
    }

    public function store(UserModelRequest $request)
    {
        return new UserModelResource(UserModel::create($request->validated()));
    }

    public function show(UserModel $userModel)
    {
        return new UserModelResource($userModel);
    }

    public function update(UserModelRequest $request, UserModel $userModel)
    {
        $userModel->update($request->validated());

        return new UserModelResource($userModel);
    }

    public function destroy(UserModel $userModel)
    {
        $userModel->delete();

        return response()->json();
    }
}
