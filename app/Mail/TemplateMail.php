<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TemplateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $viewFile;
    public $data;
    public $subjectLine;
    public $replyToEmail;

    public function __construct(
        string $viewFile,
        array $data = [],
        string $subjectLine = 'Notification',
        ?string $replyToEmail = null
    ) {
        $this->viewFile = $viewFile;
        $this->data = $data;
        $this->subjectLine = $subjectLine;
        $this->replyToEmail = $replyToEmail;
    }

    public function build()
    {
        $mail = $this->subject($this->subjectLine)
            ->view($this->viewFile)
            ->with($this->data);

        if ($this->replyToEmail) {
            $mail->replyTo($this->replyToEmail);
        }

        return $mail;
    }

    public static function sendTo(
        string $email,
        string $viewFile,
        array $data = [],
        string $subjectLine = 'Notification',
        ?string $replyToEmail = null
    ) {
        try {
            \Log::info('Mail sending started', [
                'to' => $email,
                'view' => $viewFile,
                'data' => $data,
                'subject' => $subjectLine
            ]);
            $replyToEmail = $replyToEmail ?: setting('admin_email');
            Mail::to($email)->queue(new self($viewFile, $data, $subjectLine, $replyToEmail));
             \Log::info('Mail sent successfully', [
                'to' => $email
            ]);
            //Mail::to($email)->queue(new self($viewFile, $data, $subjectLine, $replyToEmail));

            // QUEUE_CONNECTION=database
            // php artisan queue:table
            // php artisan migrate
            // php artisan queue:work
            // php artisan queue:work --tries=3      
        } catch (\Exception $e) {
            \Log::error('Mail send failed: ' . $e->getMessage());

            throw $e;
        }

    }
    public static function sendToOld(
        string $email,
        string $viewFile,
        array $data = [],
        string $subjectLine = 'Notification',
        ?string $replyToEmail = null
    ) {
        try {
            $replyToEmail = $replyToEmail ?: setting('admin_email');

            \Log::info('Mail sending started', [
                'to' => $email,
                'view' => $viewFile,
                'subject' => $subjectLine
            ]);
            app()->forgetInstance('mailer');


            Mail::to($email)->send(
                new self($viewFile, $data, $subjectLine, $replyToEmail)
            );

            \Log::info('Mail sent successfully', [
                'to' => $email
            ]);

        } catch (\Exception $e) {
            \Log::error('Mail send failed: ' . $e->getMessage());

            throw $e;
        }
    }

    public static function sendToAdmin(
        string $viewFile,
        array $data = [],
        string $subjectLine = 'Admin Notification',
        ?string $replyToEmail = null
    ) {
        self::sendTo(setting('admin_email'), $viewFile, $data, $subjectLine, $replyToEmail);
    }
}