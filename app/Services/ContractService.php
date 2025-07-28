<?php

namespace App\Services;

use App\Mails\TeacherContractMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContractService
{
    /**
     * Génère le contrat PDF et l'envoie par email à l'enseignant.
     *
     * @param array $contractData Les données à injecter dans le contrat.
     * @param string $teacherEmail L'adresse email de l'enseignant.
     * @return bool
     */
    public static function generateAndSendContract(array $contractData, string $teacherEmail): bool
    {
        try {
            // Générer le PDF à partir de la vue Blade
            $pdf = Pdf::loadView('contracts.teacher_contract', ['data' => $contractData]);

            // Sauvegarder le PDF temporairement pour l'attacher à l'email
            $pdfPath = storage_path('app/public/contracts/contract_' . uniqid() . '.pdf');
            $pdf->save($pdfPath);

            // Envoyer l'email avec le PDF attaché
//            Mail::to($teacherEmail)->send(new TeacherContractMail($pdfPath, $contractData['nom_enseignant'] ?? 'Enseignant'));
            Mail::to("atidiane741@gmail.com")->send(new TeacherContractMail($pdfPath, $contractData['nom_enseignant'] ?? 'Enseignant'));
            // Supprimer le fichier temporaire après l'envoi
            unlink($pdfPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération ou de l\'envoi du contrat : ' . $e->getMessage());
            // Gérer les erreurs (log, etc.)
            // Log::error('Erreur lors de la génération ou de l'envoi du contrat : ' . $e->getMessage());
            return false;
        }
    }
}
