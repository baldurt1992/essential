<?php

namespace App\Http\Controllers\Client;

use App\Domain\Billing\Models\DownloadLicense;
use App\Domain\Billing\Models\Purchase;
use App\Domain\Billing\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientSubscriptionResource;
use App\Http\Resources\Client\ClientPurchaseResource;
use App\Http\Resources\Client\ClientLicenseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function subscriptions(Request $request): JsonResponse
    {
        $subscriptions = Subscription::query()
            ->where('user_id', $request->user()->id)
            ->with(['plan'])
            ->latest()
            ->get();

        return ClientSubscriptionResource::collection($subscriptions)->response();
    }

    public function purchases(Request $request): JsonResponse
    {
        $purchases = Purchase::query()
            ->where('user_id', $request->user()->id)
            ->with(['template'])
            ->latest('purchased_at')
            ->get();

        return ClientPurchaseResource::collection($purchases)->response();
    }

    public function licenses(Request $request): JsonResponse
    {
        $licenses = DownloadLicense::query()
            ->where('user_id', $request->user()->id)
            ->with(['template', 'source'])
            ->latest()
            ->get();

        return ClientLicenseResource::collection($licenses)->response();
    }
}

