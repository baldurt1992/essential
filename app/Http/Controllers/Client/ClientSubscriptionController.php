<?php

namespace App\Http\Controllers\Client;

use App\Domain\Billing\Contracts\BillingGateway;
use App\Domain\Billing\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientSubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClientSubscriptionController extends Controller
{
    public function __construct(private readonly BillingGateway $billingGateway)
    {
    }

    public function cancel(Request $request, Subscription $subscription): JsonResponse
    {
        // Verificar que la suscripción pertenece al usuario autenticado
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para cancelar esta suscripción.',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->billingGateway->cancelSubscription($subscription, true);
        } catch (Throwable $exception) {
            Log::error('Failed to cancel subscription', [
                'subscription_id' => $subscription->getKey(),
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos cancelar la suscripción, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $subscription->refresh();

        return (new ClientSubscriptionResource($subscription->loadMissing(['plan'])))->response();
    }
}

