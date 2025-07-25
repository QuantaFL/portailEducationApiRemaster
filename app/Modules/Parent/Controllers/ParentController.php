<?php

namespace App\Modules\Parent\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parent\Models\ParentModel;
use App\Modules\Parent\Requests\ParentRequest;
use App\Modules\Parent\Ressources\ParentResource;
use App\Modules\User\Models\UserModel;

class ParentController extends Controller
{
    public function index()
    {
        return response()->json(ParentResource::collection(ParentModel::all()));
    }

    public function store(ParentRequest $request)
    {
        $user = UserModel::create($request->user);
        $parentModel = new ParentModel();
      //  $parentModel->hire_date = $request->hire_date;
        $parentModel->user_model_id = $user->id;
        $parentModel->save();
        return response()->json(new ParentResource($parentModel));
    }

    public function show(Parent $parent)
    {
        return response()->json(new ParentResource($parent));
    }

    public function update(ParentRequest $request, Parent $parent)
    {
        $parent->update($request->validated());

        return response()->json(new ParentResource($parent));
    }

    public function destroy(Parent $parent)
    {
        $parent->delete();

        return response()->json();
    }
}
