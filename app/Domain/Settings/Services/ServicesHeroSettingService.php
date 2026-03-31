<?php

namespace App\Domain\Settings\Services;

use App\Domain\Settings\Models\ServicesHeroSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServicesHeroSettingService
{
    public const DISK = 'public';

    public const DIRECTORY = 'services-hero';

    /** Ruta bajo /public cuando no hay video subido. */
    public const FALLBACK_ASSET = 'videos/main-home-video-2.mp4';

    public const MAX_UPLOAD_KILOBYTES = 51200;

    public function getSetting(): ServicesHeroSetting
    {
        return ServicesHeroSetting::query()->firstOrCreate([]);
    }

    public function publicVideoUrl(?ServicesHeroSetting $setting = null): string
    {
        $setting ??= ServicesHeroSetting::query()->first();

        if ($setting
            && filled($setting->hero_video_path)
            && Storage::disk(self::DISK)->exists($setting->hero_video_path)) {
            $relative = str_replace('\\', '/', $setting->hero_video_path);
            $url = '/storage/'.$relative;
            if ($setting->updated_at) {
                $url .= '?cb='.$setting->updated_at->getTimestamp();
            }

            return $url;
        }

        return '/'.ltrim(self::FALLBACK_ASSET, '/');
    }

    public function replaceHeroVideo(User $user, UploadedFile $file): ServicesHeroSetting
    {
        $setting = $this->getSetting();
        $previousPath = $setting->hero_video_path;

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if (! in_array($extension, ['mp4', 'webm'], true)) {
            $extension = $file->getMimeType() === 'video/webm' ? 'webm' : 'mp4';
        }

        $filename = Str::uuid()->toString().'.'.$extension;
        $storedPath = $file->storeAs(self::DIRECTORY, $filename, self::DISK);

        if ($previousPath
            && Str::startsWith($previousPath, self::DIRECTORY.'/')
            && Storage::disk(self::DISK)->exists($previousPath)) {
            Storage::disk(self::DISK)->delete($previousPath);
        }

        $setting->hero_video_path = $storedPath;
        $setting->updated_by = $user->getKey();
        $setting->save();

        Log::info('[ServicesHeroSetting] Video de cabecera reemplazado', [
            'user_id' => $user->getKey(),
            'path' => $storedPath,
        ]);

        return $setting->refresh();
    }
}
