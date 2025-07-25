<?php

namespace App\Modules\Subject\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Requests\SubjectRequest;
use App\Modules\Subject\Ressources\SubjectResource;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(SubjectResource::collection(Subject::all()));
    }

    public function store(SubjectRequest $request)
    {
        return response()->json(new SubjectResource(Subject::create($request->validated())));
    }

    public function show(Subject $subject)
    {
        return response()->json(new SubjectResource($subject));
    }

    public function update(SubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());

        return response()->json(new SubjectResource($subject));
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return response()->json();
    }
}
