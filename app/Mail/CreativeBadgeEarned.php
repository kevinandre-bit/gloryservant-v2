<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreativeBadgeEarned extends Mailable
{
    use Queueable, SerializesModels;

    public $badge;
    public $person;

    public function __construct($badge, $person)
    {
        $this->badge = $badge;
        $this->person = $person;
    }

    public function build()
    {
        return $this->subject('🏆 Badge Earned: ' . $this->badge->name)
                    ->view('emails.creative.badge-earned');
    }
}