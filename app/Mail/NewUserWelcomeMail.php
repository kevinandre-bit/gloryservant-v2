<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $idno;

    public function __construct($email, $password, $idno)
    {
        $this->email = $email;
        $this->password = $password;
        $this->idno = $idno;
    }

    public function build()
    {
        return $this->subject('Welcome to GloryServant!')
                    ->view('emails.new_user_welcome');
    }
}