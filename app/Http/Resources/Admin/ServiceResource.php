<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin \App\Domain\Catalog\Models\Service */
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'image_url' => $this->getImageUrl(),
            'link_url' => $this->link_url,
            'is_active' => $this->is_active,
            'is_popular' => $this->is_popular,
            'sort_order' => $this->sort_order,
            'metadata' => $this->metadata ?? [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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
}
