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

    public function show(ParentModel $parent)
    {
        return response()->json(new ParentResource($parent));
    }

    public function update(ParentRequest $request, ParentModel $parent)
    {
        $parentRequest = $request->validated();

        $refUser = UserModel::findOrFail($parent->user_model_id);

        $refUser->update($parentRequest['user']);

        // parentModel has no personal fields; in this case, only the user fields are updated


        return response()->json(new ParentResource($parent));
    }

    public function destroy(ParentModel $parent)
    {
        $parent->delete();

        return response()->json();
    }
}
