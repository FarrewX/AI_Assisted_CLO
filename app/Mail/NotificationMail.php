<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    public $subjectText;

    /**
     * Create a new message instance.
     */
    public function __construct($content, $subjectText = 'แจ้งเตือนจากระบบ Laravel')
    {
        $this->content = $content;
        $this->subjectText = $subjectText;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('message');
    }
    
}
