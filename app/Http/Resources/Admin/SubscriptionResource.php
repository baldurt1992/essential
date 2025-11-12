<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Billing\Models\Subscription */
class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'cancel_at_period_end' => $this->cancel_at_period_end,
            'trial_ends_at' => $this->trial_ends_at,
            'starts_at' => $this->starts_at,
            'current_period_start' => $this->current_period_start,
            'current_period_end' => $this->current_period_end,
            'cancel_at' => $this->cancel_at,
            'canceled_at' => $this->canceled_at,
            'ends_at' => $this->ends_at,
            'metadata' => $this->metadata ?? [],
            'stripe_subscription_id' => $this->stripe_subscription_id,
            'stripe_customer_id' => $this->stripe_customer_id,
            'stripe_price_id' => $this->stripe_price_id,
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
