<?php

namespace App\Infrastructure\Billing\Stripe;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Stripe\Checkout\Session as StripeSession;
use Stripe\StripeClient;
use Stripe\Subscription as StripeSubscription;

class StripeBillingGateway implements BillingGateway
{
    public function __construct(private readonly StripeClient $stripe) {}

    public function createSubscriptionCheckoutSession(User $user, Plan $plan, array $options = []): StripeSession
    {
        if (! $plan->stripe_price_id) {
            throw new InvalidArgumentException('Plan does not have an associated Stripe price ID.');
        }

        $payload = [
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [
                [
                    'price' => $plan->stripe_price_id,
                    'quantity' => Arr::get($options, 'quantity', 1),
                ],
            ],
            'allow_promotion_codes' => true,
            'success_url' => Arr::get($options, 'success_url', config('billing.stripe.success_url')),
            'cancel_url' => Arr::get($options, 'cancel_url', config('billing.stripe.cancel_url')),
            'subscription_data' => [
                'metadata' => array_merge(
                    [
                        'plan_uuid' => $plan->uuid,
                        'user_id' => $user->getKey(),
                    ],
                    Arr::get($options, 'subscription_metadata', []),
                ),
                'trial_period_days' => Arr::get($options, 'trial_period_days', config('billing.stripe.subscription_trial_days')),
            ],
            'metadata' => array_merge(
                [
                    'plan_uuid' => $plan->uuid,
                    'user_id' => $user->getKey(),
                    'session_type' => 'subscription',
                ],
                Arr::get($options, 'metadata', []),
            ),
        ];

        return $this->stripe->checkout->sessions->create($payload);
    }

    public function createPublicSubscriptionCheckoutSession(Plan $plan, ?string $customerEmail = null, array $options = []): StripeSession
    {
        if (! $plan->stripe_price_id) {
            throw new InvalidArgumentException('Plan does not have an associated Stripe price ID.');
        }

        $payload = [
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $plan->stripe_price_id,
                    'quantity' => Arr::get($options, 'quantity', 1),
                ],
            ],
            'allow_promotion_codes' => true,
            'success_url' => Arr::get($options, 'success_url', config('billing.stripe.success_url')),
            'cancel_url' => Arr::get($options, 'cancel_url', config('billing.stripe.cancel_url')),
            'subscription_data' => [
                'metadata' => array_merge(
                    [
                        'plan_uuid' => $plan->uuid,
                    ],
                    Arr::get($options, 'subscription_metadata', []),
                ),
                'trial_period_days' => Arr::get($options, 'trial_period_days', config('billing.stripe.subscription_trial_days')),
            ],
            'metadata' => array_merge(
                [
                    'plan_uuid' => $plan->uuid,
                    'session_type' => 'public_subscription',
                ],
                Arr::get($options, 'metadata', []),
            ),
        ];

        // Add customer email if provided (optional for public checkout)
        if ($customerEmail) {
            $payload['customer_email'] = $customerEmail;
        }

        return $this->stripe->checkout->sessions->create($payload);
    }

    public function createOneTimeCheckoutSession(User $user, array $payload, array $options = []): StripeSession
    {
        $lineItem = Arr::only($payload, ['price', 'quantity', 'adjustable_quantity', 'tax_rates']);

        if (! isset($lineItem['price'])) {
            throw new InvalidArgumentException('A price identifier is required to create a one-time checkout session.');
        }

        $sessionPayload = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [array_merge(['quantity' => 1], $lineItem)],
            'success_url' => Arr::get($options, 'success_url', config('billing.stripe.success_url')),
            'cancel_url' => Arr::get($options, 'cancel_url', config('billing.stripe.cancel_url')),
            'metadata' => array_merge(
                [
                    'user_id' => $user->getKey(),
                    'session_type' => 'one_time',
                ],
                Arr::get($options, 'metadata', []),
            ),
        ];

        if ($description = Arr::get($payload, 'description')) {
            $sessionPayload['line_items'][0]['description'] = $description;
        }

        if ($clientReferenceId = Arr::get($options, 'client_reference_id')) {
            $sessionPayload['client_reference_id'] = $clientReferenceId;
        }

        return $this->stripe->checkout->sessions->create($sessionPayload);
    }

    public function retrieveSubscription(string $stripeSubscriptionId): StripeSubscription
    {
        return $this->stripe->subscriptions->retrieve($stripeSubscriptionId);
    }

    public function cancelSubscription(Subscription $subscription, bool $atPeriodEnd = true): StripeSubscription
    {
        if (! $subscription->stripe_subscription_id) {
            throw new InvalidArgumentException('Subscription does not have an associated Stripe subscription ID.');
        }

        $response = $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id, [
            'cancel_at_period_end' => $atPeriodEnd,
        ]);

        Log::info('Stripe subscription canceled', [
            'local_subscription_id' => $subscription->getKey(),
            'stripe_subscription_id' => $subscription->stripe_subscription_id,
            'at_period_end' => $atPeriodEnd,
        ]);

        return $response;
    }
}
