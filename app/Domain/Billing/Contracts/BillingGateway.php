<?php

namespace App\Domain\Billing\Contracts;

use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use App\Models\User;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Subscription as StripeSubscription;

interface BillingGateway
{
    public function createSubscriptionCheckoutSession(User $user, Plan $plan, array $options = []): StripeSession;

    public function createOneTimeCheckoutSession(User $user, array $payload, array $options = []): StripeSession;

    public function retrieveSubscription(string $stripeSubscriptionId): StripeSubscription;

    public function cancelSubscription(Subscription $subscription, bool $atPeriodEnd = true): StripeSubscription;
}
