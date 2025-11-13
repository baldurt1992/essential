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
            'subscription_data' => array_filter([
                'metadata' => array_merge(
                    [
                        'plan_uuid' => $plan->uuid,
                        'user_id' => $user->getKey(),
                    ],
                    Arr::get($options, 'subscription_metadata', []),
                ),
                'trial_period_days' => ($trialDays = Arr::get($options, 'trial_period_days', config('billing.stripe.subscription_trial_days'))) > 0 ? $trialDays : null,
            ], fn($value) => $value !== null),
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
            'subscription_data' => array_filter([
                'metadata' => array_merge(
                    [
                        'plan_uuid' => $plan->uuid,
                    ],
                    Arr::get($options, 'subscription_metadata', []),
                ),
                'trial_period_days' => ($trialDays = Arr::get($options, 'trial_period_days', config('billing.stripe.subscription_trial_days'))) > 0 ? $trialDays : null,
            ], fn($value) => $value !== null),
            'metadata' => array_merge(
                [
                    'plan_uuid' => $plan->uuid,
                    'session_type' => 'public_subscription',
                    'user_id' => Arr::get($options, 'user_id'),
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
        $lineItem = [];

        // Si hay un price ID, usarlo directamente
        if (isset($payload['price'])) {
            $lineItem = [
                'price' => $payload['price'],
                'quantity' => Arr::get($payload, 'quantity', 1),
            ];

            // Agregar campos opcionales permitidos
            if (isset($payload['adjustable_quantity'])) {
                $lineItem['adjustable_quantity'] = $payload['adjustable_quantity'];
            }
            if (isset($payload['tax_rates'])) {
                $lineItem['tax_rates'] = $payload['tax_rates'];
            }
        } else {
            // Si no hay price, construir price_data con los datos del payload
            $amount = Arr::get($payload, 'amount');
            $currency = Arr::get($payload, 'currency', 'usd');
            $name = Arr::get($payload, 'name', 'Item');
            $description = Arr::get($payload, 'description');

            if (! $amount) {
                throw new InvalidArgumentException('Either a price identifier or amount is required to create a checkout session.');
            }

            $priceData = [
                'currency' => $currency,
                'unit_amount' => $amount,
                'product_data' => [
                    'name' => $name,
                ],
            ];

            if ($description) {
                $priceData['product_data']['description'] = $description;
            }

            $lineItem = [
                'price_data' => $priceData,
                'quantity' => Arr::get($payload, 'quantity', 1),
            ];
        }

        $sessionPayload = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [$lineItem],
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

        if ($clientReferenceId = Arr::get($options, 'client_reference_id')) {
            $sessionPayload['client_reference_id'] = $clientReferenceId;
        }

        return $this->stripe->checkout->sessions->create($sessionPayload);
    }

    public function createGuestCheckoutSession(string $email, array $payload, array $options = []): StripeSession
    {
        $lineItem = [];

        // Si hay un price ID, usarlo directamente
        if (isset($payload['price'])) {
            $lineItem = [
                'price' => $payload['price'],
                'quantity' => Arr::get($payload, 'quantity', 1),
            ];
        } else {
            // Si no hay price, construir price_data con los datos del payload
            $amount = Arr::get($payload, 'amount');
            $currency = Arr::get($payload, 'currency', 'usd');
            $name = Arr::get($payload, 'name', 'Item');
            $description = Arr::get($payload, 'description');

            if (! $amount) {
                throw new InvalidArgumentException('Either a price identifier or amount is required to create a checkout session.');
            }

            $priceData = [
                'currency' => $currency,
                'unit_amount' => $amount,
                'product_data' => [
                    'name' => $name,
                ],
            ];

            if ($description) {
                $priceData['product_data']['description'] = $description;
            }

            $lineItem = [
                'price_data' => $priceData,
                'quantity' => Arr::get($payload, 'quantity', 1),
            ];
        }

        $sessionPayload = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $email,
            'line_items' => [$lineItem],
            'success_url' => Arr::get($options, 'success_url', config('billing.stripe.success_url')),
            'cancel_url' => Arr::get($options, 'cancel_url', config('billing.stripe.cancel_url')),
            'metadata' => array_merge(
                [
                    'customer_email' => $email,
                    'session_type' => 'guest_purchase',
                ],
                Arr::get($options, 'metadata', []),
            ),
        ];

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

        // If canceling at period end, use update() instead of cancel()
        if ($atPeriodEnd) {
            $response = $this->stripe->subscriptions->update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);
        } else {
            // Cancel immediately
            $response = $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);
        }

        Log::info('Stripe subscription canceled', [
            'local_subscription_id' => $subscription->getKey(),
            'stripe_subscription_id' => $subscription->stripe_subscription_id,
            'at_period_end' => $atPeriodEnd,
        ]);

        return $response;
    }

    public function reactivateSubscription(Subscription $subscription): StripeSubscription
    {
        if (! $subscription->stripe_subscription_id) {
            throw new InvalidArgumentException('Subscription does not have an associated Stripe subscription ID.');
        }

        // Reactivate by setting cancel_at_period_end to false
        $response = $this->stripe->subscriptions->update($subscription->stripe_subscription_id, [
            'cancel_at_period_end' => false,
        ]);

        Log::info('Stripe subscription reactivated', [
            'local_subscription_id' => $subscription->getKey(),
            'stripe_subscription_id' => $subscription->stripe_subscription_id,
        ]);

        return $response;
    }
}
