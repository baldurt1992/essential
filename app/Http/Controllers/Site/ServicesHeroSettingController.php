<?php

namespace App\Http\Controllers\Site;

use App\Domain\Settings\Services\ServicesHeroSettingService;
use App\Http\Controllers\Controller;
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
}
