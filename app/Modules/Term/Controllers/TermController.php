<?php

namespace App\Modules\Term\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Session\Models\AcademicYear;
use App\Modules\Session\Models\StatutsSessionEnum;
use App\Modules\Term\Models\Term;
use App\Modules\Term\Requests\TermRequest;
use App\Modules\Term\Ressources\TermResource;
use Illuminate\Validation\Rule;

class TermController extends Controller
{
    public function index()
    {
        return response()->json(TermResource::collection(Term::all()));
    }

    public function store(TermRequest $request)
    {
        $academicYear = AcademicYear::find($request->academic_year_id);
        if( $academicYear->status !== StatutsSessionEnum::EN_COURS){
            return response()->json([
                'message' => 'on ne peut ajouter de période à une année inactive.',
            ], 400);
        }
        $exists = Term::where('name', $request->name)
            ->where('academic_year_id', $request->academic_year_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'cette prériode existe déjà pour cette année .',
            ], 400);
        }

        $term = Term::create($request->validated());

        return response()->json(new TermResource($term), 201);
    }

    public function show(Term $term)
    {
        return response()->json(new TermResource($term));
    }

    public function update(TermRequest $request, Term $term)
    {
        $exists = Term::where('name', $request->name)
            ->where('academic_year_id', $request->academic_year_id)
            ->exists();

        if (!$exists) {
            return response()->json([
                'message' => ' aucune  prériode correspondante pour l\'année  recherché.',
            ], 400);
        }

        $term->update($request->validated());

        return response()->json(new TermResource($term));
    }

    public function destroy(Term $term)
    {
        $term->delete();

        return response()->json();
    }
}
