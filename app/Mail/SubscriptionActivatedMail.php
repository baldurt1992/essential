<?php

namespace App\Mail;

use App\Domain\Billing\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivatedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Subscription $subscription) {}

    public function build(): self
    {
        return $this
            ->subject('Tu suscripción está activa')
            ->markdown('emails.subscription-activated', [
                'subscription' => $this->subscription,
                'user' => $this->subscription->user,
                'plan' => $this->subscription->plan,
            ]);
    }
}
