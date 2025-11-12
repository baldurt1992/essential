<?php

namespace App\Http\Controllers\Site;

use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Services\PlanService;
use App\Domain\Billing\Services\SubscriptionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\CreatePlanCheckoutRequest;
use App\Http\Resources\PublicPlanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly SubscriptionService $subscriptionService,
    ) {
    }

    public function index(): JsonResponse
    {
        $plans = $this->planService->getPublicPlans();

        return PublicPlanResource::collection($plans)->response();
    }

    public function checkout(Plan $plan, CreatePlanCheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $session = $this->subscriptionService->createPublicCheckoutSession(
                $plan,
                $validated['email'] ?? null,
                [
                    'success_url' => $validated['success_url'] ?? null,
                    'cancel_url' => $validated['cancel_url'] ?? null,
                ]
            );

            return response()->json([
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No pudimos crear la sesión de checkout. Por favor, intenta de nuevo.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}


