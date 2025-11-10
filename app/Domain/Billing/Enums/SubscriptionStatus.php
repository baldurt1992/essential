<?php

namespace App\Domain\Billing\Enums;

enum SubscriptionStatus: string
{
    case Trialing = 'trialing';
    case Active = 'active';
    case PastDue = 'past_due';
    case Canceled = 'canceled';
    case Incomplete = 'incomplete';
    case IncompleteExpired = 'incomplete_expired';
    case Unpaid = 'unpaid';
    case Paused = 'paused';

    public static function fromStripe(string $status): self
    {
        return match ($status) {
            'trialing' => self::Trialing,
            'active' => self::Active,
            'past_due' => self::PastDue,
            'canceled' => self::Canceled,
            'incomplete' => self::Incomplete,
            'incomplete_expired' => self::IncompleteExpired,
            'unpaid' => self::Unpaid,
            'paused' => self::Paused,
            default => self::Active,
        };
    }
}
