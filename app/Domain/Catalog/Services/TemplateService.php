<?php

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Template;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class TemplateService
{
    public function __construct(private readonly StripeClient $stripe) {}

    public function store(User $user, array $data): Template
    {
        $template = new Template();
        $template->fill($this->mapStoreData($data));
        $template->created_by = $user->getKey();

        $this->handleUploads($template, $data);

        $template->save();

        $this->syncStripe($template, (float) $data['price']);

        return $template->refresh();
    }

    public function update(Template $template, array $data): Template
    {
        $originalPrice = $template->price_cents;

        $template->fill($this->mapUpdateData($template, $data));
        $this->handleUploads($template, $data);
        $template->save();

        if (array_key_exists('price', $data) && $this->priceChanged($originalPrice, (float) $data['price'])) {
            $this->syncStripe($template, (float) $data['price'], regeneratePrice: true);
        }

        return $template->refresh();
    }

    public function delete(Template $template): void
    {
        $this->deleteFile($template->preview_image_path, 'public');
        $this->deleteFile($template->download_path, 'local');

        $template->delete();
    }

    private function mapStoreData(array $data): array
    {
        return [
            'title' => $data['title'],
            'slug' => $data['slug'] ?? null,
            'description' => $data['description'] ?? null,
            'price_cents' => $this->toCents((float) $data['price']),
            'currency' => config('billing.currency', 'eur'),
            'tags' => Arr::get($data, 'tags', []),
            'metadata' => Arr::get($data, 'metadata', []),
            'is_active' => Arr::get($data, 'is_active', true),
            'is_popular' => Arr::get($data, 'is_popular', false),
            'is_new' => Arr::get($data, 'is_new', false),
            'sort_order' => Arr::get($data, 'sort_order', 0),
        ];
    }

    private function mapUpdateData(Template $template, array $data): array
    {
        $payload = [];

        if (array_key_exists('title', $data)) {
            $payload['title'] = $data['title'];
        }

        if (array_key_exists('slug', $data)) {
            $payload['slug'] = $data['slug'];
        }

        if (array_key_exists('description', $data)) {
            $payload['description'] = $data['description'];
        }

        if (array_key_exists('price', $data)) {
            $payload['price_cents'] = $this->toCents((float) $data['price']);
        }

        if (array_key_exists('tags', $data)) {
            $payload['tags'] = Arr::get($data, 'tags', []);
        }

        if (array_key_exists('metadata', $data)) {
            $payload['metadata'] = Arr::get($data, 'metadata', $template->metadata ?? []);
        }

        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (bool) $data['is_active'];
        }

        if (array_key_exists('is_popular', $data)) {
            $payload['is_popular'] = (bool) $data['is_popular'];
        }

        if (array_key_exists('is_new', $data)) {
            $payload['is_new'] = (bool) $data['is_new'];
        }

        if (array_key_exists('sort_order', $data)) {
            $payload['sort_order'] = (int) $data['sort_order'];
        }

        return $payload;
    }

    private function handleUploads(Template $template, array $data): void
    {
        if ($preview = Arr::get($data, 'preview_image')) {
            $this->deleteFile($template->preview_image_path, 'public');
            $template->preview_image_path = $this->storeFile($preview, 'templates/previews', 'public');
        }

        if ($package = Arr::get($data, 'package_file')) {
            $this->deleteFile($template->download_path, 'local');
            $template->download_path = $this->storeFile($package, 'templates/packages', 'local');
        }
    }

    private function syncStripe(Template $template, float $price, bool $regeneratePrice = false): void
    {
        if (! $template->stripe_product_id) {
            $product = $this->stripe->products->create([
                'name' => $template->title,
                'description' => $template->description,
                'metadata' => [
                    'type' => 'template_asset',
                    'template_uuid' => $template->uuid ?? Str::uuid()->toString(),
                ],
            ]);

            $template->stripe_product_id = $product->id;
            $template->save();
        }

        if ($regeneratePrice || ! $template->stripe_price_id) {
            $price = $this->stripe->prices->create([
                'unit_amount' => $this->toCents($price),
                'currency' => config('billing.currency', 'eur'),
                'product' => $template->stripe_product_id,
            ]);

            $template->stripe_price_id = $price->id;
            $template->save();
        }
    }

    private function priceChanged(int $originalPriceCents, float $newPrice): bool
    {
        return $originalPriceCents !== $this->toCents($newPrice);
    }

    private function storeFile(UploadedFile $file, string $directory, string $disk): string
    {
        return $file->store($directory, ['disk' => $disk]);
    }

    private function deleteFile(?string $path, string $disk): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    private function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
