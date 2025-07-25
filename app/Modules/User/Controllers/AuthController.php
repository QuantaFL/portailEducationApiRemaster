<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date',
            'email' => 'required|string|email|max:255|unique:user_models',
            'password' => 'required|string|min:6',
            'adress' => 'nullable|string',
            'phone' => 'nullable|string',
            'role_id' => 'required|exists:role_models,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = UserModel::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthday' => $request->birthday,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adress' => $request->adress,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
        ]);
        $user->load('role');

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => new \App\Modules\User\Ressources\UserModelResource($user),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();
        $user->load('role');

        return response()->json([
            'user' => new \App\Modules\User\Ressources\UserModelResource($user),
            'token' => $token,
        ]);
    }
}
