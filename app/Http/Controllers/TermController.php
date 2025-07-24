<?php

namespace App\Http\Controllers;

use App\Http\Requests\TermRequest;
use App\Http\Resources\TermResource;
use App\Models\Term;

class TermController extends Controller
{
    public function index()
    {
        return TermResource::collection(Term::all());
    }

    public function store(TermRequest $request)
    {
        return new TermResource(Term::create($request->validated()));
    }

    public function show(Term $term)
    {
        return new TermResource($term);
    }

    public function update(TermRequest $request, Term $term)
    {
        $term->update($request->validated());

        return new TermResource($term);
    }

    public function destroy(Term $term)
    {
        $term->delete();

        return response()->json();
    }
}
