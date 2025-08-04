<?php

namespace App\Services;

use App\Mails\TeacherContractMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class ContractService
{
    /**
     * Generate a PDF contract and send it via email to the teacher.
     *
     * This method creates a PDF from a Blade view, saves it temporarily,
     * sends it as an email attachment, and then deletes the temporary file.
     *
     * @param  array  $contractData The data to be injected into the contract view.
     * @param  string  $teacherEmail The teacher's email address.
     * @return bool True on success, false on failure.
     */
    public static function generateAndSendContract(array $contractData, string $teacherEmail): bool
    {
        try {
            $pdf = Pdf::loadView('contracts.teacher_contract', ['data' => $contractData]);

            $pdfPath = storage_path('app/public/contracts/contract_' . uniqid() . '.pdf');
            $pdf->save($pdfPath);

            // Send the email with the attached PDF
            Mail::to($teacherEmail)->send(new TeacherContractMail($pdfPath, $contractData['nom_enseignant'] ?? 'Enseignant'));

            // Delete the temporary file after sending
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return true;
        } catch (Exception $e) {
            Log::error('Error generating or sending contract: ' . $e->getMessage());
            return false;
        }
    }
}
