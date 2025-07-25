<?php

namespace App\Modules\Term\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Term\Models\Term;
use App\Modules\Term\Requests\TermRequest;
use App\Modules\Term\Ressources\TermResource;

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
}
