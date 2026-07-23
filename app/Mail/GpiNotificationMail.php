<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GpiNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $title,
        public string $message,
        public string $recipientName = 'Utilisateur',
        public ?string $actionUrl = null,
        public ?string $actionLabel = 'Ouvrir dans GPI'
    ) {}

    public function build(): static
    {
        return $this->subject($this->mailSubject)
            ->view('emails.gpi-notification', [
                'title' => $this->title,
                'bodyText' => $this->message,
                'recipientName' => $this->recipientName,
                'actionUrl' => $this->actionUrl,
                'actionLabel' => $this->actionLabel,
            ]);
    }
}
