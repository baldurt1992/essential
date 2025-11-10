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
            'preview_url' => $this->preview_image_path ? Storage::disk('public')->url($this->preview_image_path) : null,
            'tags' => $this->tags ?? [],
            'metadata' => $this->metadata ?? [],
            'is_accessible' => $this->when(isset($this->is_accessible), (bool) $this->is_accessible),
            'created_at' => $this->created_at,
        ];
    }
}
