<?php

namespace App\Modules\Term\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Term\Models\Term;
use App\Modules\Term\Requests\TermRequest;
use App\Modules\Term\Ressources\TermResource;
use App\Modules\AcademicYear\Models\AcademicYear;

class TermController extends Controller
{
    public function index()
    {
        return response()->json(TermResource::collection(Term::all()));
    }

    public function store(TermRequest $request)
    {
        return response()->json(new TermResource(Term::create($request->validated())));
    }

    public function show(Term $term)
    {
        return response()->json(new TermResource($term));
    }

    public function update(TermRequest $request, Term $term)
    {
        $term->update($request->validated());

        return response()->json(new TermResource($term));
    }

    public function destroy(Term $term)
    {
        $term->delete();

        return response()->json();
    }

    public function getCurrentTerm()
    {
        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return response()->json(['message' => 'No current academic year found.'], 404);
        }

        $currentTerm = $currentAcademicYear->terms()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$currentTerm) {
            return response()->json(['message' => 'No current term found for the current academic year.'], 404);
        }

        return response()->json(new TermResource($currentTerm));
    }
}
