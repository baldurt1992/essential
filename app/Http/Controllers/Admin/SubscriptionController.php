<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SubscriptionResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SubscriptionController extends Controller
{
    public function __construct(private readonly BillingGateway $billingGateway)
    {
    }

    public function index(): JsonResponse
    {
        $subscriptions = Subscription::query()
            ->with(['user', 'plan'])
            ->latest()
            ->paginate(20);

        return SubscriptionResource::collection($subscriptions)->response();
    }

    public function cancel(Subscription $subscription): JsonResponse
    {
        try {
            $stripeSubscription = $this->billingGateway->cancelSubscription($subscription, true);
            
            // Sincronizar el modelo local con la respuesta de Stripe
            $subscription->update([
                'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                'cancel_at' => $stripeSubscription->cancel_at ? now()->setTimestamp($stripeSubscription->cancel_at) : null,
                'canceled_at' => $stripeSubscription->canceled_at ? now()->setTimestamp($stripeSubscription->canceled_at) : null,
                'ends_at' => $stripeSubscription->ended_at ? now()->setTimestamp($stripeSubscription->ended_at) : null,
                'status' => SubscriptionStatus::fromStripe($stripeSubscription->status ?? 'active'),
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to cancel subscription', [
                'subscription_id' => $subscription->getKey(),
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos cancelar la suscripción, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $subscription->refresh();

        return (new SubscriptionResource($subscription->loadMissing(['user', 'plan'])))->response();
    }

    public function reactivate(Subscription $subscription): JsonResponse
    {
        if (! $subscription->willCancel()) {
            return response()->json([
                'message' => 'Esta suscripción no está programada para cancelarse.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $stripeSubscription = $this->billingGateway->reactivateSubscription($subscription);

            // Calculate current_period_end if not provided by Stripe
            $currentPeriodEnd = isset($stripeSubscription->current_period_end)
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : null;

            // If still null, try to calculate from current_period_start and plan interval
            if (! $currentPeriodEnd) {
                $currentPeriodStart = isset($stripeSubscription->current_period_start)
                    ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                    : $subscription->current_period_start;

                if ($currentPeriodStart && $subscription->plan) {
                    $interval = $subscription->plan->billing_interval;
                    $intervalCount = $subscription->plan->billing_interval_count ?? 1;

                    $currentPeriodEnd = match ($interval) {
                        'year' => $currentPeriodStart->copy()->addYears($intervalCount),
                        'month' => $currentPeriodStart->copy()->addMonths($intervalCount),
                        'week' => $currentPeriodStart->copy()->addWeeks($intervalCount),
                        'day' => $currentPeriodStart->copy()->addDays($intervalCount),
                        default => $currentPeriodStart->copy()->addMonths($intervalCount),
                    };
                } else {
                    // Last resort: use existing value or cancel_at
                    $currentPeriodEnd = $subscription->current_period_end ?? $subscription->cancel_at;
                }
            }

            // Update local subscription with latest data from Stripe
            $subscription->update([
                'status' => SubscriptionStatus::fromStripe($stripeSubscription->status ?? 'active'),
                'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                'current_period_start' => isset($stripeSubscription->current_period_start)
                    ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                    : $subscription->current_period_start,
                'current_period_end' => $currentPeriodEnd,
                'cancel_at' => isset($stripeSubscription->cancel_at)
                    ? Carbon::createFromTimestamp($stripeSubscription->cancel_at)
                    : null,
                'canceled_at' => isset($stripeSubscription->canceled_at)
                    ? Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                    : null,
                'ends_at' => isset($stripeSubscription->ended_at)
                    ? Carbon::createFromTimestamp($stripeSubscription->ended_at)
                    : null,
            ]);

            $subscription->refresh();
        } catch (Throwable $exception) {
            Log::error('Failed to reactivate subscription', [
                'subscription_id' => $subscription->getKey(),
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos reactivar la suscripción, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return (new SubscriptionResource($subscription->loadMissing(['user', 'plan'])))->response();
    }
}
