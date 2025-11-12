<?php

namespace App\Domain\Settings\Services;

use App\Domain\Settings\Models\ContactInformation;
use App\Models\User;
use Illuminate\Support\Arr;

class ContactInformationService
{
    public const AVAILABLE_SOCIAL_NETWORKS = [
        'website',
        'instagram',
        'facebook',
        'linkedin',
        'twitter',
        'youtube',
        'tiktok',
        'pinterest',
        'whatsapp',
        'telegram',
        'github',
        'slack',
        'reddit',
        'vimeo',
        'twitch',
    ];

    private const SOCIAL_COLUMN_MAP = [
        'instagram' => 'instagram_url',
        'facebook' => 'facebook_url',
    ];

    /**
     * Validates if a URL matches the expected domain pattern for a social network.
     */
    public static function isValidSocialUrl(string $network, ?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $domainPatterns = [
            'instagram' => ['instagram.com', 'instagr.am'],
            'facebook' => ['facebook.com', 'fb.com', 'fb.me'],
            'linkedin' => ['linkedin.com'],
            'twitter' => ['twitter.com', 'x.com'],
            'youtube' => ['youtube.com', 'youtu.be'],
            'tiktok' => ['tiktok.com'],
            'pinterest' => ['pinterest.com', 'pin.it'],
            'github' => ['github.com'],
            'reddit' => ['reddit.com'],
            'vimeo' => ['vimeo.com'],
            'twitch' => ['twitch.tv'],
            'behance' => ['behance.net'],
            'dribbble' => ['dribbble.com'],
            'whatsapp' => ['wa.me', 'whatsapp.com', 'api.whatsapp.com'],
            'telegram' => ['t.me', 'telegram.org', 'telegram.me'],
            'slack' => ['slack.com'],
            'website' => [], // Any valid URL is acceptable for website
        ];

        // Website accepts any valid URL
        if ($network === 'website') {
            return filter_var($url, FILTER_VALIDATE_URL) !== false;
        }

        $patterns = $domainPatterns[$network] ?? [];
        if (empty($patterns)) {
            return filter_var($url, FILTER_VALIDATE_URL) !== false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if (str_contains($host, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public function get(): ContactInformation
    {
        return ContactInformation::query()->firstOrCreate([]);
    }

    public function update(User $user, array $data): ContactInformation
    {
        $contact = $this->get();

        $contact->fill($this->mapData($data));
        $contact->updated_by = $user->getKey();
        $contact->save();

        return $contact->refresh();
    }

    private function mapData(array $data): array
    {
        $data = $this->mergeSocialLinksIntoColumns($data);

        return [
            'email' => Arr::get($data, 'email'),
            'phone' => Arr::get($data, 'phone'),
            'whatsapp' => Arr::get($data, 'whatsapp'),
            'location_line_one' => Arr::get($data, 'location_line_one'),
            'location_line_two' => Arr::get($data, 'location_line_two'),
            'support_hours' => Arr::get($data, 'support_hours'),
            'instagram_url' => Arr::get($data, 'instagram_url'),
            'facebook_url' => Arr::get($data, 'facebook_url'),
            'behance_url' => Arr::get($data, 'behance_url'),
            'dribbble_url' => Arr::get($data, 'dribbble_url'),
            'website_url' => Arr::get($data, 'website_url'),
            'contact_note' => Arr::get($data, 'contact_note'),
            'metadata' => Arr::get($data, 'metadata', []),
        ];
    }

    private function mergeSocialLinksIntoColumns(array $data): array
    {
        $socialLinks = collect(Arr::get($data, 'social_links', []))
            ->filter(fn ($link) => filled(Arr::get($link, 'network')) && filled(Arr::get($link, 'url')))
            ->map(fn ($link) => [
                'network' => Arr::get($link, 'network'),
                'url' => Arr::get($link, 'url'),
            ])
            ->values();

        foreach (self::SOCIAL_COLUMN_MAP as $network => $column) {
            $matching = $socialLinks->firstWhere('network', $network);

            if ($matching) {
                $data[$column] = $matching['url'];
            }
        }

        $websiteLink = $socialLinks->firstWhere('network', 'website');
        if ($websiteLink && ! Arr::has($data, 'website_url')) {
            $data['website_url'] = $websiteLink['url'];
        }

        $metadata = Arr::get($data, 'metadata', []);
        $metadata['social_links'] = $socialLinks->map(fn ($link) => [
            'network' => $link['network'],
            'url' => $link['url'],
        ])->values()->all();

        $data['metadata'] = $metadata;
        unset($data['social_links']);

        return $data;
    }
}

