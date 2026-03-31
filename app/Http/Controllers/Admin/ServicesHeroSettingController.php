<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Settings\Services\ServicesHeroSettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateServicesHeroVideoRequest;
use App\Http\Resources\ServicesHeroSettingResource;
use Illuminate\Http\JsonResponse;

class ServicesHeroSettingController extends Controller
{
    public function __construct(
        private readonly ServicesHeroSettingService $heroSettingService,
    ) {}

    public function show(): JsonResponse
    {
        $setting = $this->heroSettingService->getSetting();

        return (new ServicesHeroSettingResource($setting))->response();
    }

    public function updateVideo(UpdateServicesHeroVideoRequest $request): JsonResponse
    {
        $setting = $this->heroSettingService->replaceHeroVideo(
            $request->user(),
            $request->file('video'),
        );

        return (new ServicesHeroSettingResource($setting))->response();
    }
}
