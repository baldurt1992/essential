<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin \App\Domain\Catalog\Models\Template */
class TemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'price_cents' => $this->price_cents,
            'currency' => $this->currency,
            'stripe_product_id' => $this->stripe_product_id,
            'stripe_price_id' => $this->stripe_price_id,
            'preview_image_path' => $this->preview_image_path,
            'preview_image_url' => $this->getPreviewImageUrl(),
            'download_path' => $this->download_path,
            'tags' => $this->tags ?? [],
            'metadata' => $this->metadata ?? [],
            'is_active' => $this->is_active,
            'is_popular' => $this->is_popular,
            'is_new' => $this->is_new,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Genera la URL completa de la imagen de preview, normalizando el path
     */
    private function getPreviewImageUrl(): ?string
    {
        if (! $this->preview_image_path) {
            return null;
        }

        // Normalizar el path: remover 'storage/' del inicio si existe
        $normalizedPath = ltrim($this->preview_image_path, '/');
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, 8); // Remover 'storage/'
        }

        // Verificar primero en public_storage (directo en public/storage/)
        if (Storage::disk('public_storage')->exists($normalizedPath)) {
            return asset('storage/' . $normalizedPath);
        }

        // Fallback a public (storage/app/public/)
        if (Storage::disk('public')->exists($normalizedPath)) {
            return asset('storage/' . $normalizedPath);
        }

        return null;
    }
}
