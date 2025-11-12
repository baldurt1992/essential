<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SubscriptionController extends Controller
{
    public function __construct(private readonly BillingGateway $billingGateway)
    {
    }

    public function index(): JsonResponse
    {
        $subscriptions = Subscription::query()
            ->with(['user', 'plan'])
            ->latest()
            ->paginate(20);

        return SubscriptionResource::collection($subscriptions)->response();
    }

    public function cancel(Subscription $subscription): JsonResponse
    {
        try {
            $this->billingGateway->cancelSubscription($subscription, true);
        } catch (Throwable $exception) {
            Log::error('Failed to cancel subscription', [
                'subscription_id' => $subscription->getKey(),
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos cancelar la suscripción, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $subscription->refresh();

        return (new SubscriptionResource($subscription->loadMissing(['user', 'plan'])))->response();
    }
}
