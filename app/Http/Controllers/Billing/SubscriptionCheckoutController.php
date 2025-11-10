<?php

namespace App\Http\Controllers\Billing;

use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SubscriptionCheckoutController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required_without:plan_slug', 'integer', 'exists:plans,id'],
            'plan_slug' => ['required_without:plan_id', 'string'],
        ]);

        $user = $request->user();

        $plan = isset($validated['plan_id'])
            ? Plan::active()->findOrFail($validated['plan_id'])
            : Plan::active()->where('slug', $validated['plan_slug'])->firstOrFail();

        try {
            $session = $this->subscriptionService->createCheckoutSession($user, $plan);
        } catch (Throwable $exception) {
            Log::error('No se pudo crear la sesión de checkout', [
                'user_id' => $user->getKey(),
                'plan_id' => $plan->getKey(),
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos generar el enlace de pago en este momento, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'checkout_url' => $session->url,
            'session_id' => $session->id,
        ]);
    }
}
