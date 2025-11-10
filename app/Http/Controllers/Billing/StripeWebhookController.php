<?php

namespace App\Http\Controllers\Billing;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\CheckoutSession;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Purchase;
use App\Domain\Billing\Models\Subscription;
use App\Domain\Billing\Events\PurchaseCompleted;
use App\Domain\Billing\Events\SubscriptionActivated;
use App\Domain\Billing\Services\DownloadLicenseService;
use App\Domain\Catalog\Models\Template;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeObject;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, BillingGateway $billingGateway): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (! $secret) {
            Log::error('Stripe webhook secret is not configured.');

            return response()->json(['error' => 'Webhook misconfigured'], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature ?? '', $secret);
        } catch (SignatureVerificationException $exception) {
            Log::warning('Stripe webhook signature verification failed', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['error' => 'Invalid signature'], SymfonyResponse::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            Log::error('Stripe webhook parsing failed', [
                'exception' => $exception,
            ]);

            return response()->json(['error' => 'Invalid payload'], SymfonyResponse::HTTP_BAD_REQUEST);
        }

        $type = $event->type;
        $object = $event->data?->object;

        if (! $object instanceof StripeObject) {
            Log::warning('Stripe webhook received payload without object', ['type' => $type]);

            return response()->json(['status' => 'ignored']);
        }

        match ($type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($object, $billingGateway),
            'checkout.session.expired' => $this->handleCheckoutSessionExpired($object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($object, $billingGateway),
            default => Log::info('Unhandled Stripe webhook event', ['type' => $type]),
        };

        return response()->json(['status' => 'received']);
    }

    private function handleCheckoutSessionCompleted(StripeObject $object, BillingGateway $billingGateway): void
    {
        $mode = $object->mode ?? null;

        if ($mode === 'subscription') {
            $this->handleSubscriptionSessionCompleted($object, $billingGateway);

            return;
        }

        if ($mode === 'payment') {
            $this->handlePaymentSessionCompleted($object);

            return;
        }

        Log::info('Checkout session completed for unsupported mode', [
            'mode' => $mode,
            'session' => $object->id ?? null,
        ]);
    }

    private function handleSubscriptionSessionCompleted(StripeObject $object, BillingGateway $billingGateway): void
    {
        $session = StripeSession::constructFrom($object->toArray());
        $subscriptionId = $session->subscription;

        if (! $subscriptionId) {
            Log::warning('Stripe session completed without subscription id', ['session' => $session->id]);

            return;
        }

        $planUuid = $session->metadata['plan_uuid'] ?? null;
        $userId = $session->metadata['user_id'] ?? null;

        if (! $planUuid || ! $userId) {
            Log::warning('Stripe session missing metadata for plan or user', [
                'session' => $session->id,
                'metadata' => $session->metadata,
            ]);

            return;
        }

        $user = User::find($userId);
        $plan = Plan::where('uuid', $planUuid)->first();

        if (! $user || ! $plan) {
            Log::error('Plan or user not found for Stripe session', [
                'session' => $session->id,
                'plan_uuid' => $planUuid,
                'user_id' => $userId,
            ]);

            return;
        }

        try {
            $stripeSubscription = $billingGateway->retrieveSubscription($subscriptionId);
        } catch (Throwable $exception) {
            Log::error('Failed to retrieve Stripe subscription during checkout.session.completed', [
                'subscription_id' => $subscriptionId,
                'session' => $session->id,
                'exception' => $exception,
            ]);

            return;
        }

        $subscription = Subscription::updateOrCreate(
            ['stripe_subscription_id' => $stripeSubscription->id],
            [
                'user_id' => $user->getKey(),
                'plan_id' => $plan->getKey(),
                'stripe_customer_id' => $stripeSubscription->customer,
                'stripe_price_id' => $stripeSubscription->items->data[0]?->price?->id,
                'status' => SubscriptionStatus::fromStripe($stripeSubscription->status ?? 'active'),
                'quantity' => $stripeSubscription->items->data[0]?->quantity ?? 1,
                'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                'trial_ends_at' => $this->timestampToDateTime($stripeSubscription->trial_end ?? null),
                'starts_at' => $this->timestampToDateTime($stripeSubscription->start_date ?? null),
                'current_period_start' => $this->timestampToDateTime($stripeSubscription->current_period_start ?? null),
                'current_period_end' => $this->timestampToDateTime($stripeSubscription->current_period_end ?? null),
                'cancel_at' => $this->timestampToDateTime($stripeSubscription->cancel_at ?? null),
                'canceled_at' => $this->timestampToDateTime($stripeSubscription->canceled_at ?? null),
                'ends_at' => $this->timestampToDateTime($stripeSubscription->ended_at ?? null),
                'metadata' => $stripeSubscription->metadata ? $stripeSubscription->metadata->toArray() : null,
            ],
        );

        $this->markCheckoutSession($session->id, 'complete');

        if ($subscription->wasRecentlyCreated || $subscription->wasChanged('status')) {
            event(new SubscriptionActivated($subscription->fresh(['user', 'plan'])));
        }

        Log::info('Stripe subscription synchronized', [
            'local_subscription_id' => $subscription->getKey(),
            'stripe_subscription_id' => $stripeSubscription->id,
            'user_id' => $user->getKey(),
            'plan_id' => $plan->getKey(),
        ]);
    }

    private function handlePaymentSessionCompleted(StripeObject $object): void
    {
        $session = StripeSession::constructFrom($object->toArray());
        $templateUuid = $session->metadata['template_uuid'] ?? null;
        $userId = $session->metadata['user_id'] ?? null;

        if (! $templateUuid || ! $userId) {
            Log::warning('Stripe payment session missing metadata', [
                'session' => $session->id,
                'metadata' => $session->metadata,
            ]);

            return;
        }

        $user = User::find($userId);
        $template = Template::where('uuid', $templateUuid)->first();

        if (! $user || ! $template) {
            Log::error('Template or user not found for payment session', [
                'session' => $session->id,
                'template_uuid' => $templateUuid,
                'user_id' => $userId,
            ]);

            return;
        }

        $checkoutSession = CheckoutSession::where('stripe_session_id', $session->id)->first();

        $purchase = Purchase::updateOrCreate(
            ['stripe_session_id' => $session->id],
            [
                'user_id' => $user->getKey(),
                'template_id' => $template->getKey(),
                'checkout_session_id' => $checkoutSession?->getKey(),
                'stripe_payment_intent_id' => $session->payment_intent,
                'amount_cents' => $session->amount_total ?? $template->price_cents,
                'currency' => strtoupper($session->currency ?? $template->currency ?? 'EUR'),
                'status' => 'completed',
                'metadata' => $session->metadata ? $session->metadata->toArray() : null,
                'purchased_at' => now(),
            ],
        );

        $this->markCheckoutSession($session->id, 'complete');

        $licenseService = app(DownloadLicenseService::class);
        $license = $licenseService->issueForPurchase($purchase);

        event(new PurchaseCompleted($purchase->fresh(['user', 'template']), $license));

        Log::info('Template purchase recorded', [
            'purchase_id' => $purchase->getKey(),
            'template_id' => $template->getKey(),
            'user_id' => $user->getKey(),
            'stripe_session_id' => $session->id,
        ]);
    }

    private function handleCheckoutSessionExpired(StripeObject $object): void
    {
        $sessionId = $object->id ?? null;

        $this->markCheckoutSession($sessionId, 'expired');

        Log::info('Stripe checkout session expired', [
            'session' => $sessionId,
            'mode' => $object->mode ?? null,
        ]);
    }

    private function handleSubscriptionDeleted(StripeObject $object): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $object->id ?? null)->first();

        if (! $subscription) {
            Log::warning('Received subscription.deleted for unknown subscription', [
                'stripe_subscription_id' => $object->id ?? null,
            ]);

            return;
        }

        $subscription->update([
            'status' => SubscriptionStatus::Canceled,
            'canceled_at' => $this->timestampToDateTime($object->canceled_at ?? null),
            'ends_at' => $this->timestampToDateTime($object->ended_at ?? null),
        ]);

        Log::info('Stripe subscription marked as canceled', [
            'subscription_id' => $subscription->getKey(),
            'stripe_subscription_id' => $object->id ?? null,
        ]);
    }

    private function handleSubscriptionUpdated(StripeObject $object, BillingGateway $billingGateway): void
    {
        $stripeSubscriptionId = $object->id ?? null;

        if (! $stripeSubscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (! $subscription) {
            Log::warning('Received subscription.updated for unknown subscription', [
                'stripe_subscription_id' => $stripeSubscriptionId,
            ]);

            return;
        }

        try {
            $stripeSubscription = $billingGateway->retrieveSubscription($stripeSubscriptionId);
        } catch (Throwable $exception) {
            Log::error('Failed to retrieve Stripe subscription during subscription.updated', [
                'stripe_subscription_id' => $stripeSubscriptionId,
                'exception' => $exception,
            ]);

            return;
        }

        $subscription->update([
            'status' => SubscriptionStatus::fromStripe($stripeSubscription->status ?? 'active'),
            'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
            'current_period_start' => $this->timestampToDateTime($stripeSubscription->current_period_start ?? null),
            'current_period_end' => $this->timestampToDateTime($stripeSubscription->current_period_end ?? null),
            'cancel_at' => $this->timestampToDateTime($stripeSubscription->cancel_at ?? null),
            'canceled_at' => $this->timestampToDateTime($stripeSubscription->canceled_at ?? null),
            'ends_at' => $this->timestampToDateTime($stripeSubscription->ended_at ?? null),
            'metadata' => $stripeSubscription->metadata ? $stripeSubscription->metadata->toArray() : null,
        ]);

        Log::info('Stripe subscription updated', [
            'subscription_id' => $subscription->getKey(),
            'stripe_subscription_id' => $stripeSubscriptionId,
            'status' => $subscription->status,
        ]);
    }

    private function markCheckoutSession(?string $sessionId, string $status): void
    {
        if (! $sessionId) {
            return;
        }

        CheckoutSession::where('stripe_session_id', $sessionId)->update([
            'status' => $status,
            'completed_at' => $status === 'complete' ? now() : null,
        ]);
    }

    private function timestampToDateTime(?int $timestamp): ?Carbon
    {
        return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
