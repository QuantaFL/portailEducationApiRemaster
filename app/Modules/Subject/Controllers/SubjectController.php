<?php

namespace App\Modules\Subject\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Requests\SubjectRequest;
use App\Modules\Subject\Ressources\SubjectResource;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(SubjectResource::collection(Subject::all()));
    }

    public function store(SubjectRequest $request)
    {
        $exists = DB::table('subjects')
            ->where('name', $request->name)
            ->where('level', $request->level)
            ->exists();
        if ($exists) {
            return response()->json([
                "message" => "La matière '{$request->name}' existe déjà pour le niveau {$request->level}."
            ], 409);
        }

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

    public function getSubjectsByIds()
    {
        $ids = request()->input('ids', []);
        $subjects = Subject::whereIn('id', $ids)->get();
        return response()->json(SubjectResource::collection($subjects));
    }


}
