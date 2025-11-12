<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Settings\Models\ContactInformation */
class ContactInformationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Clean column URLs - only return if they match their network
        $instagramUrl = $this->instagram_url;
        if ($instagramUrl && !\App\Domain\Settings\Services\ContactInformationService::isValidSocialUrl('instagram', $instagramUrl)) {
            $instagramUrl = null;
        }

        $facebookUrl = $this->facebook_url;
        if ($facebookUrl && !\App\Domain\Settings\Services\ContactInformationService::isValidSocialUrl('facebook', $facebookUrl)) {
            $facebookUrl = null;
        }

        return [
            'id' => $this->id,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'location_line_one' => $this->location_line_one,
            'location_line_two' => $this->location_line_two,
            'support_hours' => $this->support_hours,
            'instagram_url' => $instagramUrl,
            'facebook_url' => $facebookUrl,
            'behance_url' => $this->behance_url,
            'dribbble_url' => $this->dribbble_url,
            'website_url' => $this->website_url,
            'contact_note' => $this->contact_note,
            'metadata' => $this->metadata ?? [],
            'social_links' => $this->resolveSocialLinks(),
            'updated_at' => $this->updated_at,
        ];
    }

    private function resolveSocialLinks(): array
    {
        $metadataLinks = data_get($this->metadata, 'social_links', []);

        $links = collect($metadataLinks)
            ->filter(fn ($link) => isset($link['network'], $link['url']))
            ->filter(fn ($link) => \App\Domain\Settings\Services\ContactInformationService::isValidSocialUrl(
                $link['network'],
                $link['url']
            ))
            ->map(fn ($link) => [
                'network' => $link['network'],
                'url' => $link['url'],
            ])
            ->values();

        if ($links->isNotEmpty()) {
            return $links->all();
        }

        // Fallback to column URLs, but validate them too
        $fallback = collect([
            'website' => $this->website_url,
            'instagram' => $this->instagram_url,
            'facebook' => $this->facebook_url,
        ])
            ->filter()
            ->filter(fn ($url, $network) => \App\Domain\Settings\Services\ContactInformationService::isValidSocialUrl(
                $network,
                $url
            ))
            ->map(fn ($url, $network) => [
                'network' => $network,
                'url' => $url,
            ])
            ->values()
            ->all();

        return $fallback;
    }
}

