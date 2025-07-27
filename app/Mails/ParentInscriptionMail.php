<?php

namespace App\Mails;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParentInscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $studentEmail;
    public $studentRole;

    public function __construct($studentName, $studentEmail, $studentRole)
    {
        $this->studentName = $studentName;
        $this->studentEmail = $studentEmail;
        $this->studentRole = $studentRole;
    }

    public function build()
    {
        // Génération du certificat d'inscription PDF pour l'élève
        $certificatPdf = Pdf::loadView('pdfs.certificat_inscription', [
            'name' => $this->studentName,
            'email' => $this->studentEmail,
            'role' => $this->studentRole,
        ])->output();

        return $this->subject('Certificat d’inscription de votre enfant')
            ->view('emails.parent_inscription')
            ->attachData($certificatPdf, 'certificat_inscription.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}

