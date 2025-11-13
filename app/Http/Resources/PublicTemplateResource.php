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

        $url = Storage::disk('public')->url($normalizedPath);
        
        // Verificar que el archivo existe antes de retornar la URL
        if (! Storage::disk('public')->exists($normalizedPath)) {
            \Log::warning('Preview image file does not exist', [
                'template_id' => $this->id,
                'normalized_path' => $normalizedPath,
                'original_path' => $this->preview_image_path,
                'full_path' => Storage::disk('public')->path($normalizedPath),
            ]);
        }

        return $url;
    }
}
