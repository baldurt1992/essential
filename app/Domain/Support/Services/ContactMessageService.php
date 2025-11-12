<?php

namespace App\Domain\Support\Services;

use App\Domain\Support\DataTransferObjects\ContactMessageData;
use App\Domain\Support\Exceptions\CannotDetermineContactRecipients;
use App\Domain\Settings\Services\ContactInformationService;
use App\Mail\ContactMessageReceived;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ContactMessageService
{
    public function __construct(
        private readonly ContactInformationService $contactInformationService,
        private readonly Mailer $mailer,
    ) {
    }

    public function handle(ContactMessageData $message): void
    {
        $recipients = $this->resolveRecipients();

        if ($recipients->isEmpty()) {
            throw CannotDetermineContactRecipients::create();
        }

        $primaryRecipient = $recipients->first();
        $bccRecipients = $recipients->slice(1)->values();

        $mailable = new ContactMessageReceived($message);

        $mailer = $this->mailer->to($primaryRecipient);

        if ($bccRecipients->isNotEmpty()) {
            $mailer->bcc($bccRecipients->all());
        }

        $mailer->send($mailable);

        Log::info('site.contact.message.received', [
            'payload' => $message->toArray(),
            'recipients' => $recipients->all(),
        ]);
    }

    private function resolveRecipients(): Collection
    {
        $contactInformation = $this->contactInformationService->get();

        $candidates = [
            $contactInformation?->email,
            Arr::get($contactInformation?->metadata, 'support_email'),
            Config::get('mail.from.address'),
            Config::get('support.contact_fallback_email'),
        ];

        return collect($candidates)
            ->filter(fn ($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();
    }
}


