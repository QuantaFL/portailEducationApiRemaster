<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionRequest;
use App\Http\Resources\SessionResource;
use App\Models\Session;

class SessionController extends Controller
{
    public function index()
    {
        return SessionResource::collection(Session::all());
    }

    public function store(SessionRequest $request)
    {
        return new SessionResource(Session::create($request->validated()));
    }

    public function show(Session $session)
    {
        return new SessionResource($session);
    }

    public function update(SessionRequest $request, Session $session)
    {
        $session->update($request->validated());

        return new SessionResource($session);
    }

    public function destroy(Session $session)
    {
        $session->delete();

        return response()->json();
    }
}
