<?php

namespace App\Http\Controllers\Client;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\DownloadLicense;
use App\Domain\Billing\Models\Purchase;
use App\Domain\Billing\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientSubscriptionResource;
use App\Http\Resources\Client\ClientPurchaseResource;
use App\Http\Resources\Client\ClientLicenseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct(private readonly BillingGateway $billingGateway)
    {
    }

    public function subscriptions(Request $request): JsonResponse
    {
        $subscriptions = Subscription::query()
            ->where('user_id', $request->user()->id)
            ->with(['plan'])
            ->latest()
            ->get();

        // Sync active subscriptions with Stripe if current_period_end is missing
        foreach ($subscriptions as $subscription) {
            if ($subscription->isActive() && ! $subscription->current_period_end && $subscription->stripe_subscription_id) {
                try {
                    $stripeSubscription = $this->billingGateway->retrieveSubscription($subscription->stripe_subscription_id);
                    
                    $currentPeriodEnd = isset($stripeSubscription->current_period_end) && $stripeSubscription->current_period_end
                        ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                        : null;
                    
                    // If Stripe doesn't provide it, calculate from current_period_start and plan
                    if (! $currentPeriodEnd && $subscription->plan) {
                        $currentPeriodStart = isset($stripeSubscription->current_period_start) && $stripeSubscription->current_period_start
                            ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                            : $subscription->current_period_start;
                        
                        if ($currentPeriodStart) {
                            $interval = $subscription->plan->billing_interval;
                            $intervalCount = $subscription->plan->billing_interval_count ?? 1;
                            
                            $currentPeriodEnd = match($interval) {
                                'year' => $currentPeriodStart->copy()->addYears($intervalCount),
                                'month' => $currentPeriodStart->copy()->addMonths($intervalCount),
                                'week' => $currentPeriodStart->copy()->addWeeks($intervalCount),
                                'day' => $currentPeriodStart->copy()->addDays($intervalCount),
                                default => $currentPeriodStart->copy()->addMonths($intervalCount),
                            };
                        }
                    }
                    
                    if ($currentPeriodEnd) {
                        $subscription->update([
                            'current_period_start' => isset($stripeSubscription->current_period_start) && $stripeSubscription->current_period_start
                                ? Carbon::createFromTimestamp($stripeSubscription->current_period_start) 
                                : $subscription->current_period_start,
                            'current_period_end' => $currentPeriodEnd,
                            'status' => SubscriptionStatus::fromStripe($stripeSubscription->status ?? 'active'),
                            'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                        ]);
                        
                        $subscription->refresh();
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to sync subscription from Stripe', [
                        'subscription_id' => $subscription->id,
                        'stripe_subscription_id' => $subscription->stripe_subscription_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return ClientSubscriptionResource::collection($subscriptions)->response();
    }

    public function purchases(Request $request): JsonResponse
    {
        $purchases = Purchase::query()
            ->where('user_id', $request->user()->id)
            ->with(['template'])
            ->latest('purchased_at')
            ->get();

        return ClientPurchaseResource::collection($purchases)->response();
    }

    public function licenses(Request $request): JsonResponse
    {
        $licenses = DownloadLicense::query()
            ->where('user_id', $request->user()->id)
            ->with(['template', 'source'])
            ->latest()
            ->get();

        return ClientLicenseResource::collection($licenses)->response();
    }
}

