<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AdminMail extends Mailable
{
    use SerializesModels;

    public $viewFile;
    public $data;
    public $subjectLine;

    public function __construct(
        string $viewFile,
        array $data = [],
        string $subjectLine = 'Admin Notification'
    ) {
        $this->viewFile = $viewFile;
        $this->data = $data;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)->view($this->viewFile)->with($this->data);
    }

    public static function sendToAdmin(
        string $viewFile,
        array $data = [],
        string $subjectLine = 'Admin Notification'
    ) {
        Mail::to(setting('admin_email'))->send(new self($viewFile, $data, $subjectLine));
    }
}