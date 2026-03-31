<?php

namespace App\Http\Requests\Admin;

use App\Domain\Settings\Services\ServicesHeroSettingService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServicesHeroVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\File>>
     */
    public function rules(): array
    {
        $maxKb = ServicesHeroSettingService::MAX_UPLOAD_KILOBYTES;

        return [
            'video' => [
                'required',
                'file',
                'mimetypes:video/mp4,video/webm',
                'max:'.$maxKb,
            ],
        ];
    }
}
