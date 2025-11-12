<?php

namespace App\Domain\Support\DataTransferObjects;

class ContactMessageData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $company,
        public readonly ?string $subject,
        public readonly string $message,
        public readonly ?string $originUrl = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            company: $data['company'] ?? null,
            subject: $data['subject'] ?? null,
            message: $data['message'],
            originUrl: $data['origin_url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'subject' => $this->subject,
            'message' => $this->message,
            'origin_url' => $this->originUrl,
        ];
    }
}


