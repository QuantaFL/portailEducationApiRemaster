<?php

namespace App\Mails;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $password;
    public $role;

    public function __construct($name, $email, $password, $role)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function build()
    {
        // Génération du certificat d'inscription PDF
        $certificatPdf = Pdf::loadView('pdfs.certificat_inscription', [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ])->output();

        // Génération du règlement intérieur PDF avec logo
        $reglementPdf = Pdf::loadView('pdfs.reglement_interieur', [
            'name' => $this->name,
            'role' => $this->role,
            'logo' => public_path('images/school_logo.png'),
        ])->output();

        return $this->subject('Bienvenue sur le portail éducatif de votre école')
            ->view('emails.user_welcome')
            ->attachData($certificatPdf, 'certificat_inscription.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($reglementPdf, 'reglement_interieur.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
