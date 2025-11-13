<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\Admin\PlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Billing\Models\Subscription */
class ClientSubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Use cancel_at as fallback for current_period_end when subscription is canceled
        // If still null and subscription is active, try to get it from Stripe
        $periodEnd = $this->current_period_end;

        if (! $periodEnd && $this->willCancel() && $this->cancel_at) {
            $periodEnd = $this->cancel_at;
        }

        // If still null and subscription is active, try to refresh from Stripe
        if (! $periodEnd && $this->isActive() && $this->stripe_subscription_id) {
            try {
                $billingGateway = app(\App\Domain\Billing\Contracts\BillingGateway::class);
                $stripeSubscription = $billingGateway->retrieveSubscription($this->stripe_subscription_id);

                if (isset($stripeSubscription->current_period_end) && $stripeSubscription->current_period_end) {
                    $periodEnd = \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end);

                    // Update the model synchronously to ensure it's saved for next time
                    \App\Domain\Billing\Models\Subscription::where('id', $this->id)
                        ->update(['current_period_end' => $periodEnd]);
                }
            } catch (\Throwable $e) {
                // Silently fail, just use null
            }
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'cancel_at_period_end' => $this->cancel_at_period_end,
            'trial_ends_at' => $this->trial_ends_at,
            'starts_at' => $this->starts_at,
            'current_period_start' => $this->current_period_start,
            'current_period_end' => $periodEnd,
            'cancel_at' => $this->cancel_at,
            'canceled_at' => $this->canceled_at,
            'ends_at' => $this->ends_at,
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'is_active' => $this->isActive(),
            'will_cancel' => $this->willCancel(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
