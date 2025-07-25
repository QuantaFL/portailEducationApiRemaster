<?php

namespace App\Modules\ReportCard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ReportCard\Models\ReportCard;
use App\Modules\ReportCard\Requests\ReportCardRequest;
use App\Modules\ReportCard\Ressources\ReportCardResource;

class ReportCardController extends Controller
{
    public function index()
    {
        return response()->json(ReportCardResource::collection(ReportCard::all()));
    }

    public function store(ReportCardRequest $request)
    {
        return response()->json(new ReportCardResource(ReportCard::create($request->validated())));
    }

    public function show(ReportCard $reportCard)
    {
        return response()->json(new ReportCardResource($reportCard));
    }

    public function update(ReportCardRequest $request, ReportCard $reportCard)
    {
        $reportCard->update($request->validated());

        return response()->json(new ReportCardResource($reportCard));
    }

    public function destroy(ReportCard $reportCard)
    {
        $reportCard->delete();

        return response()->json();
    }
}
