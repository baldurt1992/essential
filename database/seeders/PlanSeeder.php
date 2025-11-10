<?php

namespace Database\Seeders;

use App\Domain\Billing\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class PlanSeeder extends Seeder
{
    private StripeClient $stripe;

    private array $plans = [
        [
            'key' => 'monthly',
            'name' => 'Suscripción Mensual',
            'slug' => 'suscripcion-mensual',
            'description' => 'Acceso ilimitado a todas las plantillas mientras dure tu suscripción mensual.',
            'billing_interval' => 'month',
            'billing_interval_count' => 1,
            'price_cents' => 1999,
            'features' => [
                'Descarga ilimitada',
                'Nuevos diseños cada semana',
                'Soporte prioritario',
            ],
            'sort_order' => 10,
            'active' => true,
        ],
        [
            'key' => 'semiannual',
            'name' => 'Suscripción Semestral',
            'slug' => 'suscripcion-semestral',
            'description' => 'Paga menos por mes asegurando 6 meses de acceso ilimitado a las plantillas.',
            'billing_interval' => 'month',
            'billing_interval_count' => 6,
            'price_cents' => 9999,
            'features' => [
                'Descarga ilimitada',
                'Nuevos diseños cada semana',
                'Prioridad en lanzamientos',
                'Soporte directo',
            ],
            'sort_order' => 20,
            'active' => true,
        ],
        [
            'key' => 'annual',
            'name' => 'Suscripción Anual',
            'slug' => 'suscripcion-anual',
            'description' => 'La mejor tarifa por mes con acceso continuo durante 12 meses.',
            'billing_interval' => 'year',
            'billing_interval_count' => 1,
            'price_cents' => 17999,
            'features' => [
                'Descarga ilimitada',
                'Lanzamientos exclusivos',
                'Soporte premium',
                'Sesiones estratégicas trimestrales',
            ],
            'sort_order' => 30,
            'active' => true,
        ],
    ];

    public function __construct()
    {
        $secret = config('services.stripe.secret');

        if (! $secret) {
            throw new \RuntimeException('Stripe secret no configurado. Define STRIPE_SECRET antes de ejecutar PlanSeeder.');
        }

        $this->stripe = new StripeClient($secret);
    }

    public function run(): void
    {
        foreach ($this->plans as $definition) {
            $plan = Plan::query()
                ->where('metadata->key', $definition['key'])
                ->orWhere('slug', $definition['slug'])
                ->first();

            if (! $plan) {
                $plan = Plan::create([
                    'uuid' => Str::uuid(),
                    'name' => $definition['name'],
                    'slug' => $definition['slug'],
                    'description' => $definition['description'],
                    'billing_interval' => $definition['billing_interval'],
                    'billing_interval_count' => $definition['billing_interval_count'],
                    'price_cents' => $definition['price_cents'],
                    'currency' => config('billing.currency', 'eur'),
                    'is_active' => $definition['active'],
                    'features' => $definition['features'],
                    'metadata' => ['key' => $definition['key']],
                    'sort_order' => $definition['sort_order'],
                    'created_by' => null,
                ]);
            } else {
                $plan->update([
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'billing_interval' => $definition['billing_interval'],
                    'billing_interval_count' => $definition['billing_interval_count'],
                    'price_cents' => $definition['price_cents'],
                    'currency' => config('billing.currency', 'eur'),
                    'is_active' => $definition['active'],
                    'features' => $definition['features'],
                    'metadata' => array_merge($plan->metadata ?? [], ['key' => $definition['key']]),
                    'sort_order' => $definition['sort_order'],
                ]);
            }

            if ($plan->stripe_price_id && $plan->stripe_product_id) {
                continue;
            }

            $stripeProduct = $this->stripe->products->create([
                'name' => $plan->name,
                'description' => $plan->description,
                'metadata' => [
                    'type' => 'subscription_plan',
                    'plan_key' => $definition['key'],
                ],
            ]);

            $stripePrice = $this->stripe->prices->create([
                'unit_amount' => $definition['price_cents'],
                'currency' => config('billing.currency', 'eur'),
                'recurring' => [
                    'interval' => $definition['billing_interval'],
                    'interval_count' => $definition['billing_interval_count'],
                ],
                'product' => $stripeProduct->id,
            ]);

            $plan->update([
                'stripe_product_id' => $stripeProduct->id,
                'stripe_price_id' => $stripePrice->id,
            ]);
        }
    }
}
