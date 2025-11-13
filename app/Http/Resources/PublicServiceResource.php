<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin \App\Domain\Catalog\Models\Service */
class PublicServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'link_url' => $this->resolveLinkUrl(),
            'image_url' => $this->getImageUrl(),
            'is_popular' => (bool) $this->is_popular,
            'sort_order' => $this->sort_order,
            'metadata' => $this->metadata ?? [],
        ];
    }

    /**
     * Genera la URL completa de la imagen, normalizando el path
     */
    private function getImageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        // Normalizar el path: remover 'storage/' del inicio si existe
        $normalizedPath = ltrim($this->image_path, '/');
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

    private function resolveLinkUrl(): string
    {
        if (! empty($this->link_url)) {
            return $this->link_url;
        }

        return url("/servicios/{$this->slug}");
    }
}

