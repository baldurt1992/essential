<?php

namespace App\Domain\Billing\Services;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Models\CheckoutSession;
use App\Domain\Catalog\Models\Template;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Stripe\Checkout\Session as StripeSession;
use Throwable;

class PurchaseService
{
    public function __construct(private readonly BillingGateway $billingGateway) {}

    public function createCheckoutSession(User $user, Template $template, array $options = []): StripeSession
    {
        if (! $template->is_active) {
            throw new InvalidArgumentException('La plantilla no está disponible para la venta.');
        }

        if (! $template->stripe_price_id) {
            throw new InvalidArgumentException('La plantilla no tiene un precio configurado en Stripe.');
        }

        $lineItem = [
            'price' => $template->stripe_price_id,
            'quantity' => Arr::get($options, 'quantity', 1),
            'description' => $template->title,
        ];

        try {
            $session = $this->billingGateway->createOneTimeCheckoutSession($user, $lineItem, array_merge(
                [
                    'metadata' => array_merge(
                        [
                            'template_uuid' => $template->uuid,
                            'user_id' => $user->getKey(),
                            'session_type' => 'template_purchase',
                        ],
                        Arr::get($options, 'metadata', []),
                    ),
                    'client_reference_id' => $template->uuid,
                ],
                $options // Incluir todas las opciones pasadas (success_url, cancel_url, etc.)
            ));
        } catch (Throwable $exception) {
            Log::error('Error creando sesión de compra en Stripe', [
                'user_id' => $user->getKey(),
                'template_id' => $template->getKey(),
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
                'reference_type' => Template::class,
                'reference_id' => $template->getKey(),
                'metadata' => [
                    'template_uuid' => $template->uuid,
                ],
                'expires_at' => isset($session->expires_at) ? now()->setTimestamp($session->expires_at) : null,
            ],
        );

        return $session;
    }

    public function createGuestCheckoutSession(string $email, Template $template, array $options = []): StripeSession
    {
        if (! $template->is_active) {
            throw new InvalidArgumentException('La plantilla no está disponible para la venta.');
        }

        if (! $template->stripe_price_id) {
            throw new InvalidArgumentException('La plantilla no tiene un precio configurado en Stripe.');
        }

        $lineItem = [
            'price' => $template->stripe_price_id,
            'quantity' => Arr::get($options, 'quantity', 1),
            'description' => $template->title,
        ];

        try {
            $session = $this->billingGateway->createGuestCheckoutSession($email, $lineItem, array_merge(
                [
                    'metadata' => array_merge(
                        [
                            'template_uuid' => $template->uuid,
                            'customer_email' => $email,
                            'session_type' => 'guest_template_purchase',
                        ],
                        Arr::get($options, 'metadata', []),
                    ),
                    'client_reference_id' => $template->uuid,
                ],
                $options // Incluir todas las opciones pasadas (success_url, cancel_url, etc.)
            ));
        } catch (Throwable $exception) {
            Log::error('Error creando sesión de compra de invitado en Stripe', [
                'email' => $email,
                'template_id' => $template->getKey(),
                'exception' => $exception,
            ]);

            throw $exception;
        }

        // No creamos CheckoutSession para invitados ya que no tienen user_id
        // Se creará cuando se complete el pago en el webhook

        return $session;
    }
}
