<?php

namespace App\Domain\Billing\Listeners;

use App\Domain\Billing\Events\SubscriptionActivated;
use App\Mail\SubscriptionActivatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Log;

class SendSubscriptionActivatedMail implements ShouldQueue
{
    public function __construct(private readonly Mailer $mailer)
    {
    }

    public function handle(SubscriptionActivated $event): void
    {
        $subscription = $event->subscription->loadMissing('user', 'plan');

        $this->mailer->to($subscription->user->email)->send(new SubscriptionActivatedMail($subscription));

        Log::info('Subscription activation email sent', [
            'subscription_id' => $subscription->getKey(),
            'user_id' => $subscription->user_id,
            'plan_id' => $subscription->plan_id,
        ]);
    }
}
