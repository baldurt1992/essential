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
            'image_url' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'is_popular' => (bool) $this->is_popular,
            'sort_order' => $this->sort_order,
            'metadata' => $this->metadata ?? [],
        ];
    }

    private function resolveLinkUrl(): string
    {
        if (! empty($this->link_url)) {
            return $this->link_url;
        }

        return url("/servicios/{$this->slug}");
    }
}

