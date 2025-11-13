<?php

namespace App\Http\Controllers\Client;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientSubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClientSubscriptionController extends Controller
{
    public function __construct(private readonly BillingGateway $billingGateway) {}

    public function cancel(Request $request, Subscription $subscription): JsonResponse
    {
        // Verificar que la suscripción pertenece al usuario autenticado
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para cancelar esta suscripción.',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $stripeSubscription = $this->billingGateway->cancelSubscription($subscription, true);

            // Update local subscription with latest data from Stripe
            $currentPeriodEnd = isset($stripeSubscription->current_period_end)
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : null;

            // If current_period_end is null but cancel_at exists, use cancel_at as fallback
            if (! $currentPeriodEnd && isset($stripeSubscription->cancel_at)) {
                $currentPeriodEnd = Carbon::createFromTimestamp($stripeSubscription->cancel_at);
            }

            $subscription->update([
                'status' => SubscriptionStatus::fromStripe($stripeSubscription->status ?? 'active'),
                'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                'current_period_start' => isset($stripeSubscription->current_period_start)
                    ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                    : $subscription->current_period_start,
                'current_period_end' => $currentPeriodEnd ?? $subscription->current_period_end,
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
            Log::error('Failed to cancel subscription', [
                'subscription_id' => $subscription->getKey(),
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos cancelar la suscripción, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return (new ClientSubscriptionResource($subscription->loadMissing(['plan'])))->response();
    }

    public function reactivate(Request $request, Subscription $subscription): JsonResponse
    {
        // Verificar que la suscripción pertenece al usuario autenticado
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para reactivar esta suscripción.',
            ], Response::HTTP_FORBIDDEN);
        }

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
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos reactivar la suscripción, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return (new ClientSubscriptionResource($subscription->loadMissing(['plan'])))->response();
    }
}
