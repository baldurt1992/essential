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
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'is_active' => $this->isActive(),
            'will_cancel' => $this->willCancel(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

