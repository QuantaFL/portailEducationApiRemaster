<?php

namespace App\Modules\Teacher\Services;

use App\Mails\TeacherContractMail;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\User\Models\UserModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherContractService
{
    /**
     * Generate contract PDF and send email to teacher
     */
    public function generateAndSendContract(Teacher $teacher): bool
    {
        Log::info('TeacherContractService: Starting contract generation and email send', [
            'teacher_id' => $teacher->id,
            'user_id' => $teacher->user_model_id
        ]);

        try {
            // Get teacher data for contract
            $contractData = $this->prepareContractData($teacher);
            
            // Generate PDF
            $pdfPath = $this->generateContractPDF($contractData);
            
            // Send email with PDF attachment
            $this->sendContractEmail($teacher, $pdfPath);
            
            // Clean up temporary PDF file
            $this->cleanupTempFile($pdfPath);
            
            Log::info('TeacherContractService: Contract generated and email sent successfully', [
                'teacher_id' => $teacher->id,
                'email' => $teacher->userModel->email
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('TeacherContractService: Failed to generate contract or send email', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Prepare contract data from teacher information
     */
    private function prepareContractData(Teacher $teacher): array
    {
        $user = $teacher->userModel;
        
        Log::debug('TeacherContractService: Preparing contract data', [
            'teacher_id' => $teacher->id,
            'user_name' => $user->first_name . ' ' . $user->last_name
        ]);

        // Base contract data - you can customize these values based on your school's information
        $contractData = [
            // School information
            'nom_etablissement' => config('school.name', 'ÉTABLISSEMENT SCOLAIRE'),
            'adresse_etablissement' => config('school.address', 'ADRESSE DE L\'ÉTABLISSEMENT'),
            'telephone_etablissement' => config('school.phone', 'TÉLÉPHONE'),
            'email_etablissement' => config('school.email', 'EMAIL@SCHOOL.COM'),
            'statut_juridique' => config('school.legal_status', 'STATUT JURIDIQUE'),
            'ninea_etablissement' => config('school.ninea', 'NINEA'),
            'nom_representant_legal' => config('school.legal_representative_name', 'REPRÉSENTANT LÉGAL'),
            'qualite_representant_legal' => config('school.legal_representative_title', 'DIRECTEUR'),
            
            // Teacher information
            'nom_enseignant' => $user->last_name,
            'prenom_enseignant' => $user->first_name,
            'date_lieu_naissance_enseignant' => $user->birthday ? $user->birthday : 'NON RENSEIGNÉ',
            'nationalite_enseignant' => $user->nationality ?? 'NON RENSEIGNÉ',
            'cni_passeport_enseignant' => 'NON RENSEIGNÉ', // Could be added to user model later
            'adresse_enseignant' => $user->adress,
            'telephone_enseignant' => $user->phone,
            'email_enseignant' => $user->email,
            
            // Contract details
            'matieres_enseignees' => 'À DÉFINIR SELON ASSIGNEMENTS',
            'niveaux_enseignement' => 'À DÉFINIR SELON ASSIGNEMENTS',
            'date_prise_fonction' => $teacher->hire_date,
            'mode_paiement' => config('school.payment_method', 'VIREMENT BANCAIRE'),
            'nombre_heures_travail' => config('school.work_hours', '40'),
            'jour_debut_semaine' => config('school.week_start', 'LUNDI'),
            'jour_fin_semaine' => config('school.week_end', 'VENDREDI'),
            'ville_tribunal_competent' => config('school.tribunal_city', 'DAKAR'),
            
            // System generated
            'lieu_signature' => config('school.city', 'DAKAR'),
            'date_signature' => now()->format('d/m/Y'),
        ];

        Log::debug('TeacherContractService: Contract data prepared', [
            'teacher_name' => $contractData['nom_enseignant'] . ' ' . $contractData['prenom_enseignant'],
            'hire_date' => $contractData['date_prise_fonction']
        ]);

        return $contractData;
    }

    /**
     * Generate PDF contract
     */
    private function generateContractPDF(array $contractData): string
    {
        Log::info('TeacherContractService: Generating PDF contract');

        try {
            // Generate PDF using the blade template
            $pdf = Pdf::loadView('contracts.teacher_contract', [
                'data' => $contractData
            ]);
            
            // Set PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path(),
            ]);

            // Create temporary file path
            $fileName = 'contract_' . time() . '_' . uniqid() . '.pdf';
            $filePath = storage_path('app/temp/' . $fileName);
            
            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Save PDF to temporary file
            $pdf->save($filePath);
            
            Log::info('TeacherContractService: PDF generated successfully', [
                'file_path' => $filePath,
                'file_size' => filesize($filePath)
            ]);

            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('TeacherContractService: Failed to generate PDF', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send contract email with PDF attachment
     */
    private function sendContractEmail(Teacher $teacher, string $pdfPath): void
    {
        $user = $teacher->userModel;
        $teacherName = $user->first_name . ' ' . $user->last_name;
        
        Log::info('TeacherContractService: Sending contract email', [
            'teacher_id' => $teacher->id,
            'email' => $user->email,
            'teacher_name' => $teacherName
        ]);

        try {
            Mail::to($user->email)->send(new TeacherContractMail($pdfPath, $teacherName));
            
            Log::info('TeacherContractService: Contract email sent successfully', [
                'teacher_id' => $teacher->id,
                'email' => $user->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('TeacherContractService: Failed to send contract email', [
                'teacher_id' => $teacher->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Clean up temporary PDF file
     */
    private function cleanupTempFile(string $filePath): void
    {
        try {
            if (file_exists($filePath)) {
                unlink($filePath);
                Log::debug('TeacherContractService: Temporary PDF file cleaned up', [
                    'file_path' => $filePath
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('TeacherContractService: Failed to cleanup temporary file', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }
}