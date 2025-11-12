<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Billing\Models\Plan */
class PublicPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'currency' => $this->currency,
            'price' => $this->price,
            'price_cents' => (int) $this->price_cents,
            'billing_interval' => $this->billing_interval,
            'billing_interval_count' => (int) $this->billing_interval_count,
            'highlight' => [
                'is_popular' => (bool) data_get($this->metadata, 'is_popular', false),
                'is_recommended' => (bool) data_get($this->metadata, 'is_recommended', false),
                'label' => data_get($this->metadata, 'highlight_label'),
            ],
            'features' => $this->resolveFeatures(),
            'cta' => [
                'checkout_url' => data_get($this->metadata, 'checkout_url'),
                'contact_label' => data_get($this->metadata, 'contact_label'),
            ],
            'limits' => data_get($this->metadata, 'limits', []),
            'metadata' => $this->metadata ?? [],
        ];
    }

    private function resolveFeatures(): array
    {
        $features = data_get($this->metadata, 'features', []);
        if (! is_array($features)) {
            return [];
        }

        return collect($features)
            ->filter(fn ($feature) => filled($feature))
            ->map(fn ($feature) => (string) $feature)
            ->values()
            ->all();
    }
}


