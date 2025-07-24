<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportCardRequest;
use App\Http\Resources\ReportCardResource;
use App\Models\ReportCard;

class ReportCardController extends Controller
{
    public function index()
    {
        return ReportCardResource::collection(ReportCard::all());
    }

    public function store(ReportCardRequest $request)
    {
        return new ReportCardResource(ReportCard::create($request->validated()));
    }

    public function show(ReportCard $reportCard)
    {
        return new ReportCardResource($reportCard);
    }

    public function update(ReportCardRequest $request, ReportCard $reportCard)
    {
        $reportCard->update($request->validated());

        return new ReportCardResource($reportCard);
    }

    public function destroy(ReportCard $reportCard)
    {
        $reportCard->delete();

        return response()->json();
    }
}
