<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Billing\Models\Plan */
class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'billing_interval' => $this->billing_interval,
            'billing_interval_count' => $this->billing_interval_count,
            'price' => $this->price,
            'price_cents' => $this->price_cents,
            'currency' => $this->currency,
            'stripe_product_id' => $this->stripe_product_id,
            'stripe_price_id' => $this->stripe_price_id,
            'features' => $this->features ?? [],
            'metadata' => $this->metadata ?? [],
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'download_limit' => $this->download_limit,
            'unlimited_downloads' => $this->unlimited_downloads,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
