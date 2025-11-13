<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin \App\Domain\Catalog\Models\Template */
class PublicTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'preview_url' => $this->getPreviewImageUrl(),
            'tags' => $this->tags ?? [],
            'metadata' => $this->metadata ?? [],
            'is_popular' => (bool) $this->is_popular,
            'is_new' => (bool) $this->is_new,
            'is_accessible' => $this->when(isset($this->is_accessible), (bool) $this->is_accessible),
            'created_at' => $this->created_at,
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
        
        // Log warning si el archivo no existe en ningún disco
        \Log::warning('Preview image file does not exist', [
            'template_id' => $this->id,
            'normalized_path' => $normalizedPath,
            'original_path' => $this->preview_image_path,
        ]);

        return null;
    }
}
