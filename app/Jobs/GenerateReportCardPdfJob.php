<?php

namespace App\Jobs;

use App\Modules\ReportCard\Models\ReportCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class GenerateReportCardPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportCardId;
    protected $detailedData;

    public function __construct(int $reportCardId, array $detailedData)
    {
        $this->reportCardId = $reportCardId;
        $this->detailedData = $detailedData;
    }

    public function handle(): void
    {
        Log::info("GenerateReportCardPdfJob started for ReportCard ID: {$this->reportCardId}");
        try {
            $reportCard = ReportCard::find($this->reportCardId);

            if (!$reportCard) {
                Log::error("ReportCard with ID {$this->reportCardId} not found for PDF generation.");
                return;
            }

            $data = $this->detailedData;

            $pdf = Pdf::loadView('reports.bulletin', $data);

            $fileName = 'bulletin_' . $data['student_info']['matricule'] . '_' . $data['term_info']['name'] . '.pdf';
            $filePath = 'public/report_cards/' . $fileName;

            $pdf->save(storage_path('app/' . $filePath));

            $reportCard->path = $filePath;
            $reportCard->save();

        } catch (\Throwable $e) {
            Log::error("Error generating PDF for ReportCard ID {$this->reportCardId}: " . $e->getMessage());
            // $reportCard->update(['pdf_generation_status' => 'failed']);
        }
    }
}
