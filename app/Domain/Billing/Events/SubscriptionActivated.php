<?php

namespace App\Domain\Billing\Events;

use App\Domain\Billing\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Subscription $subscription) {}
}
