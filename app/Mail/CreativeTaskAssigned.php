<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreativeTaskAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $assignee;

    public function __construct($task, $assignee)
    {
        $this->task = $task;
        $this->assignee = $assignee;
    }

    public function build()
    {
        return $this->subject('New Creative Task Assigned: ' . $this->task->title)
                    ->view('emails.creative.task-assigned');
    }
}