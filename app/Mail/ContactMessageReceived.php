<?php

namespace App\Mail;

use App\Domain\Support\DataTransferObjects\ContactMessageData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly ContactMessageData $messageData)
    {
    }

    public function build(): self
    {
        return $this->subject('Nuevo mensaje desde Essential')
            ->markdown('emails.contact.message_received', [
                'messageData' => $this->messageData,
            ]);
    }
}


