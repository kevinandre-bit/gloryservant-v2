<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationData;

    public function __construct(array $notificationData)
{
    $this->notificationData = $notificationData;
}

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject('New Notification - GloryServant')
                    ->view('emails.notificationEmail')
                    ->with([
                        'notificationData' => $this->notificationData
                    ]);
    }
}