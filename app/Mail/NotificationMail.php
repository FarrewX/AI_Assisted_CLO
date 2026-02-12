<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content; // ต้องเป็น public
    public $subjectText;

    public function __construct($content, $subjectText)
    {
        $this->content = $content;
        $this->subjectText = $subjectText;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                ->view('mail_template'); 
    }
}
