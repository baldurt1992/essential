<?php

namespace App\Http\Controllers\Billing;

use App\Domain\Billing\Services\PurchaseService;
use App\Domain\Catalog\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PurchaseCheckoutController extends Controller
{
    public function __construct(private readonly PurchaseService $purchaseService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_id' => ['required_without:template_slug', 'integer', 'exists:templates,id'],
            'template_slug' => ['required_without:template_id', 'string'],
        ]);

        $user = $request->user();

        $template = isset($validated['template_id'])
            ? Template::active()->findOrFail($validated['template_id'])
            : Template::active()->where('slug', $validated['template_slug'])->firstOrFail();

        try {
            $session = $this->purchaseService->createCheckoutSession($user, $template);
        } catch (Throwable $exception) {
            Log::error('No se pudo crear la sesión de compra', [
                'user_id' => $user->getKey(),
                'template_id' => $template->getKey(),
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'No pudimos generar el enlace de pago para esta plantilla, intenta nuevamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'checkout_url' => $session->url,
            'session_id' => $session->id,
        ]);
    }
}
