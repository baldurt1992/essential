<?php

namespace App\Providers;

use App\Domain\Billing\Events\PurchaseCompleted;
use App\Domain\Billing\Events\SubscriptionActivated;
use App\Domain\Billing\Listeners\SendPurchaseCompletedMail;
use App\Domain\Billing\Listeners\SendSubscriptionActivatedMail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PurchaseCompleted::class => [
            SendPurchaseCompletedMail::class,
        ],
        SubscriptionActivated::class => [
            SendSubscriptionActivatedMail::class,
        ],
    ];
}
