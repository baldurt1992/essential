<?php

namespace App\Domain\Billing\Services;

use App\Domain\Billing\Models\Plan;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Throwable;

class PlanService
{
    public function __construct(private readonly StripeClient $stripe) {}

    public function getPublicPlans(): \Illuminate\Support\Collection
    {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price_cents')
            ->get();
    }

    public function store(User $user, array $data): Plan
    {
        $plan = new Plan();
        $plan->fill($this->mapStoreData($data));
        $plan->currency = config('billing.currency', 'eur');
        $plan->created_by = $user->getKey();

        $plan->save();

        $this->syncStripe($plan, (float) $data['price']);

        return $plan->refresh();
    }

    public function update(Plan $plan, array $data): Plan
    {
        $originalPrice = $plan->price_cents;
        $originalInterval = [$plan->billing_interval, $plan->billing_interval_count];

        $plan->fill($this->mapUpdateData($plan, $data));
        $plan->save();

        $priceChanged = array_key_exists('price', $data) && $originalPrice !== $this->toCents((float) $data['price']);
        $intervalChanged = (
            array_key_exists('billing_interval', $data) && $data['billing_interval'] !== $originalInterval[0]
        ) || (
            array_key_exists('billing_interval_count', $data) && (int) $data['billing_interval_count'] !== $originalInterval[1]
        );

        if ($priceChanged || $intervalChanged || empty($plan->stripe_price_id)) {
            $this->syncStripe($plan, (float) Arr::get($data, 'price', $plan->price), regeneratePrice: true);
        }

        return $plan->refresh();
    }

    public function delete(Plan $plan): void
    {
        try {
            $plan->delete();
        } catch (Throwable $exception) {
            Log::error('Failed to delete plan', [
                'plan_id' => $plan->getKey(),
                'exception' => $exception,
            ]);

            throw $exception;
        }
    }

    private function mapStoreData(array $data): array
    {
        return [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? null,
            'description' => $data['description'] ?? null,
            'billing_interval' => $data['billing_interval'],
            'billing_interval_count' => (int) $data['billing_interval_count'],
            'price_cents' => $this->toCents((float) $data['price']),
            'features' => Arr::get($data, 'features', []),
            'metadata' => Arr::get($data, 'metadata', []),
            'is_active' => Arr::get($data, 'is_active', true),
            'sort_order' => Arr::get($data, 'sort_order', 0),
        ];
    }

    private function mapUpdateData(Plan $plan, array $data): array
    {
        $payload = [];

        if (array_key_exists('name', $data)) {
            $payload['name'] = $data['name'];
        }

        if (array_key_exists('slug', $data)) {
            $payload['slug'] = $data['slug'];
        }

        if (array_key_exists('description', $data)) {
            $payload['description'] = $data['description'];
        }

        if (array_key_exists('billing_interval', $data)) {
            $payload['billing_interval'] = $data['billing_interval'];
        }

        if (array_key_exists('billing_interval_count', $data)) {
            $payload['billing_interval_count'] = (int) $data['billing_interval_count'];
        }

        if (array_key_exists('price', $data)) {
            $payload['price_cents'] = $this->toCents((float) $data['price']);
        }

        if (array_key_exists('features', $data)) {
            $payload['features'] = Arr::get($data, 'features', []);
        }

        if (array_key_exists('metadata', $data)) {
            $payload['metadata'] = Arr::get($data, 'metadata', $plan->metadata ?? []);
        }

        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (bool) $data['is_active'];
        }

        if (array_key_exists('sort_order', $data)) {
            $payload['sort_order'] = (int) $data['sort_order'];
        }

        return $payload;
    }

    private function syncStripe(Plan $plan, float $price, bool $regeneratePrice = false): void
    {
        if (! $plan->stripe_product_id) {
            $product = $this->stripe->products->create([
                'name' => $plan->name,
                'description' => $plan->description,
                'metadata' => [
                    'type' => 'subscription_plan',
                    'plan_uuid' => $plan->uuid,
                ],
            ]);

            $plan->stripe_product_id = $product->id;
            $plan->save();
        }

        if ($regeneratePrice || ! $plan->stripe_price_id) {
            $priceResponse = $this->stripe->prices->create([
                'unit_amount' => $this->toCents($price),
                'currency' => $plan->currency ?? config('billing.currency', 'eur'),
                'recurring' => [
                    'interval' => $plan->billing_interval,
                    'interval_count' => $plan->billing_interval_count,
                ],
                'product' => $plan->stripe_product_id,
            ]);

            $plan->stripe_price_id = $priceResponse->id;
            $plan->save();
        }
    }

    private function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
