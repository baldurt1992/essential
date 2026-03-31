<?php

namespace App\Http\Resources;

use App\Domain\Settings\Services\ServicesHeroSettingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Settings\Models\ServicesHeroSetting */
class ServicesHeroSettingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $service = app(ServicesHeroSettingService::class);

        return [
            'video_url' => $service->publicVideoUrl($this->resource),
            'uses_uploaded_video' => filled($this->hero_video_path),
            'updated_at' => $this->updated_at,
        ];
    }
}
