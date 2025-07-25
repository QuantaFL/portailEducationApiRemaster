<?php

namespace App\Modules\Session\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Session\Models\Session;
use App\Modules\Session\Requests\SessionRequest;
use App\Modules\Session\Ressources\SessionResource;

class SessionController extends Controller
{
    public function index()
    {
        return response()->json(SessionResource::collection(Session::all()));
    }

    public function store(SessionRequest $request)
    {
        return response()->json(new SessionResource(Session::create($request->validated())));
    }

    public function show(Session $session)
    {
        return response()->json(new SessionResource($session));
    }

    public function update(SessionRequest $request, Session $session)
    {
        $session->update($request->validated());

        return response()->json(new SessionResource($session));
    }

    public function destroy(Session $session)
    {
        $session->delete();

        return response()->json();
    }
}
