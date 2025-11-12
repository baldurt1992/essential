<?php

namespace App\Http\Controllers\Site;

use App\Domain\Catalog\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Resources\PublicServiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $servicesQuery = Service::query()->active();

        if ($request->boolean('popular')) {
            $servicesQuery->popular();
        }

        $services = $servicesQuery
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return PublicServiceResource::collection($services)->response();
    }
}

