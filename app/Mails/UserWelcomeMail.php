<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
        return $this->subject('Bienvenue sur le portail éducatif quanta je teste rekkkkkkkkkkkkkkkkkkkk ne me jettez pas la pierre')
            ->view('emails.user_welcome');
    }
}

