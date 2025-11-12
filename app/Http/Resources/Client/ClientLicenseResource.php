<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\PublicTemplateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Billing\Models\DownloadLicense */
class ClientLicenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'purchase_code' => $this->purchase_code,
            'download_count' => $this->download_count,
            'download_limit' => $this->download_limit,
            'expires_at' => $this->expires_at,
            'last_downloaded_at' => $this->last_downloaded_at,
            'can_download' => $this->canDownload(),
            'source_type' => $this->source_type,
            'template' => new PublicTemplateResource($this->whenLoaded('template')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

