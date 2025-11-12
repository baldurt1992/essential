<?php

namespace App\Domain\Billing\Services;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Models\CheckoutSession;
use App\Domain\Billing\Models\Plan;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Stripe\Checkout\Session as StripeSession;
use Throwable;

class SubscriptionService
{
    public function __construct(private readonly BillingGateway $billingGateway) {}

    public function createCheckoutSession(User $user, Plan $plan, array $options = []): StripeSession
    {
        if (! $user->hasRole('client')) {
            throw new InvalidArgumentException('Solo los clientes pueden iniciar un checkout de suscripción.');
        }

        try {
            $session = $this->billingGateway->createSubscriptionCheckoutSession($user, $plan, $options);
        } catch (Throwable $exception) {
            Log::error('Error creando sesión de checkout en Stripe', [
                'user_id' => $user->getKey(),
                'plan_id' => $plan->getKey(),
                'exception' => $exception,
            ]);

            throw $exception;
        }

        CheckoutSession::updateOrCreate(
            ['stripe_session_id' => $session->id],
            [
                'user_id' => $user->getKey(),
                'mode' => $session->mode,
                'status' => $session->status ?? 'open',
                'reference_type' => Plan::class,
                'reference_id' => $plan->getKey(),
                'metadata' => array_merge(
                    ['plan_uuid' => $plan->uuid],
                    Arr::get($options, 'metadata', []),
                ),
                'expires_at' => isset($session->expires_at) ? now()->setTimestamp($session->expires_at) : null,
            ],
        );

        return $session;
    }

    /**
     * Create a public checkout session for a plan (no authentication required).
     * The user will be created or linked after successful payment via webhook.
     */
    public function createPublicCheckoutSession(Plan $plan, ?string $customerEmail = null, array $options = []): StripeSession
    {
        if (! $plan->is_active) {
            throw new InvalidArgumentException('El plan no está disponible para la compra.');
        }

        if (! $plan->stripe_price_id) {
            throw new InvalidArgumentException('El plan no tiene un precio configurado en Stripe.');
        }

        try {
            $session = $this->billingGateway->createPublicSubscriptionCheckoutSession($plan, $customerEmail, $options);
        } catch (Throwable $exception) {
            Log::error('Error creando sesión de checkout público en Stripe', [
                'plan_id' => $plan->getKey(),
                'customer_email' => $customerEmail,
                'exception' => $exception,
            ]);

            throw $exception;
        }

        CheckoutSession::updateOrCreate(
            ['stripe_session_id' => $session->id],
            [
                'user_id' => null, // Will be set after webhook processes the payment
                'mode' => $session->mode,
                'status' => $session->status ?? 'open',
                'reference_type' => Plan::class,
                'reference_id' => $plan->getKey(),
                'metadata' => array_merge(
                    [
                        'plan_uuid' => $plan->uuid,
                        'customer_email' => $customerEmail,
                        'is_public' => true,
                    ],
                    Arr::get($options, 'metadata', []),
                ),
                'expires_at' => isset($session->expires_at) ? now()->setTimestamp($session->expires_at) : null,
            ],
        );

        return $session;
    }
}
