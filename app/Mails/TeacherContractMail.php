<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfPath;
    public $teacherName;

    /**
     * Create a new message instance.
     *
     * @param string $pdfPath Le chemin vers le fichier PDF temporaire.
     * @param string $teacherName Le nom de l'enseignant.
     * @return void
     */
    public function __construct(string $pdfPath, string $teacherName)
    {
        $this->pdfPath = $pdfPath;
        $this->teacherName = $teacherName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Votre Contrat de Travail')
                    ->view('emails.teacher_contract_email') // Créez cette vue pour le corps de l'email
                    ->attach($this->pdfPath, [
                        'as' => 'contrat_travail_' . str_replace(' ', '_', $this->teacherName) . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
