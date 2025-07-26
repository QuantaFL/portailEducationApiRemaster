<?php

namespace App\Jobs;

use App\Modules\ReportCard\Models\ReportCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateReportCardRanksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $classModelId;
    protected $termId;

    public function __construct(int $classModelId, int $termId)
    {
        $this->classModelId = $classModelId;
        $this->termId = $termId;
    }

    public function handle(): void
    {
        try {
            $reportCards = ReportCard::where('term_id', $this->termId)
                ->whereHas('studentSession', function ($query) {
                    $query->where('class_model_id', $this->classModelId);
                })
                ->orderByDesc('average_grade')
                ->get();

            $currentRank = 1;
            $previousAverage = null;
            $rankCounter = 0;

            foreach ($reportCards as $reportCard) {
                $rankCounter++;
                if ($reportCard->average_grade !== $previousAverage) {
                    $currentRank = $rankCounter;
                }
                $reportCard->rank = $currentRank;
                $reportCard->save();
                $previousAverage = $reportCard->average_grade;
            }

            Log::info("Ranks calculated successfully for Class ID: {$this->classModelId}, Term ID: {$this->termId}");

        } catch (\Exception $e) {
            Log::error("Error calculating ranks for Class ID: {$this->classModelId}, Term ID: {$this->termId}: " . $e->getMessage());
        }
    }
}
