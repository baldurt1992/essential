<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class TemplateSeeder extends Seeder
{
    private StripeClient $stripe;

    private array $templates = [
        [
            'key' => 'flyer_summer_party',
            'title' => 'Flyer Summer Party',
            'slug' => 'flyer-summer-party',
            'description' => 'Plantilla editable para fiestas de verano con tipografía vibrante y placeholders para DJ.',
            'price_cents' => 1499,
            'preview_image_path' => 'storage/templates/previews/flyer-summer.jpg',
            'download_path' => 'storage/templates/packages/flyer-summer.zip',
            'tags' => ['flyer', 'summer', 'party'],
            'sort_order' => 10,
            'active' => true,
        ],
        [
            'key' => 'flyer_luxury_night',
            'title' => 'Flyer Luxury Night',
            'slug' => 'flyer-luxury-night',
            'description' => 'Diseño elegante para eventos VIP, incluye variantes en PSD y AI.',
            'price_cents' => 1999,
            'preview_image_path' => 'storage/templates/previews/flyer-luxury.jpg',
            'download_path' => 'storage/templates/packages/flyer-luxury.zip',
            'tags' => ['flyer', 'luxury', 'nightlife'],
            'sort_order' => 20,
            'active' => true,
        ],
        [
            'key' => 'poster_festival',
            'title' => 'Poster Electronic Festival',
            'slug' => 'poster-electronic-festival',
            'description' => 'Poster multi-formato para festivales electrónicos; incluye exportación lista para impresión.',
            'price_cents' => 2499,
            'preview_image_path' => 'storage/templates/previews/poster-festival.jpg',
            'download_path' => 'storage/templates/packages/poster-festival.zip',
            'tags' => ['poster', 'festival', 'electronic'],
            'sort_order' => 30,
            'active' => true,
        ],
    ];

    public function __construct()
    {
        $secret = config('services.stripe.secret');

        if (! $secret) {
            throw new \RuntimeException('Stripe secret no configurado. Define STRIPE_SECRET antes de ejecutar TemplateSeeder.');
        }

        $this->stripe = new StripeClient($secret);
    }

    public function run(): void
    {
        foreach ($this->templates as $definition) {
            $template = Template::query()
                ->where('metadata->key', $definition['key'])
                ->orWhere('slug', $definition['slug'])
                ->first();

            if (! $template) {
                $template = Template::create([
                    'uuid' => Str::uuid(),
                    'title' => $definition['title'],
                    'slug' => $definition['slug'],
                    'description' => $definition['description'],
                    'price_cents' => $definition['price_cents'],
                    'currency' => config('billing.currency', 'eur'),
                    'preview_image_path' => $definition['preview_image_path'],
                    'download_path' => $definition['download_path'],
                    'tags' => $definition['tags'],
                    'metadata' => ['key' => $definition['key']],
                    'is_active' => $definition['active'],
                    'sort_order' => $definition['sort_order'],
                    'created_by' => null,
                ]);
            } else {
                $template->update([
                    'title' => $definition['title'],
                    'description' => $definition['description'],
                    'price_cents' => $definition['price_cents'],
                    'currency' => config('billing.currency', 'eur'),
                    'preview_image_path' => $definition['preview_image_path'],
                    'download_path' => $definition['download_path'],
                    'tags' => $definition['tags'],
                    'metadata' => array_merge($template->metadata ?? [], ['key' => $definition['key']]),
                    'is_active' => $definition['active'],
                    'sort_order' => $definition['sort_order'],
                ]);
            }

            if ($template->stripe_price_id && $template->stripe_product_id) {
                continue;
            }

            $stripeProduct = $this->stripe->products->create([
                'name' => $template->title,
                'description' => $template->description,
                'metadata' => [
                    'type' => 'template_asset',
                    'template_key' => $definition['key'],
                ],
            ]);

            $stripePrice = $this->stripe->prices->create([
                'unit_amount' => $definition['price_cents'],
                'currency' => config('billing.currency', 'eur'),
                'product' => $stripeProduct->id,
            ]);

            $template->update([
                'stripe_product_id' => $stripeProduct->id,
                'stripe_price_id' => $stripePrice->id,
            ]);
        }
    }
}
