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
    public function __construct(private readonly PurchaseService $purchaseService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_id' => ['required_without:template_slug', 'integer', 'exists:templates,id'],
            'template_slug' => ['required_without:template_id', 'string'],
            'email' => ['required_if:is_guest,true', 'email', 'max:255'],
            'is_guest' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        $isGuest = $validated['is_guest'] ?? false;

        $template = isset($validated['template_id'])
            ? Template::active()->findOrFail($validated['template_id'])
            : Template::active()->where('slug', $validated['template_slug'])->firstOrFail();

        try {
            if ($isGuest && ! $user) {
                // Compra de invitado
                if (! isset($validated['email'])) {
                    return response()->json([
                        'message' => 'El correo electrónico es requerido para compras de invitado.',
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // Usar APP_URL del .env para construir la URL correctamente
                $appUrl = rtrim(config('app.url'), '/');
                $successUrl = $appUrl . '/compra/confirmacion?session_id={CHECKOUT_SESSION_ID}';
                $cancelUrl = $appUrl . '/plantillas?purchase=' . $template->slug;

                Log::info('Creating guest checkout session', [
                    'email' => $validated['email'],
                    'template_id' => $template->getKey(),
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'app_url' => $appUrl,
                ]);

                $session = $this->purchaseService->createGuestCheckoutSession(
                    $validated['email'],
                    $template,
                    [
                        'success_url' => $successUrl,
                        'cancel_url' => $cancelUrl,
                    ]
                );
            } else {
                // Compra de usuario autenticado
                if (! $user) {
                    return response()->json([
                        'message' => 'Debes iniciar sesión para comprar esta plantilla.',
                    ], Response::HTTP_UNAUTHORIZED);
                }

                // Usar APP_URL del .env para construir la URL correctamente
                $appUrl = rtrim(config('app.url'), '/');
                $successUrl = $appUrl . '/compra/confirmacion?session_id={CHECKOUT_SESSION_ID}';
                $cancelUrl = $appUrl . '/plantillas?purchase=' . $template->slug;

                $session = $this->purchaseService->createCheckoutSession(
                    $user,
                    $template,
                    [
                        'success_url' => $successUrl,
                        'cancel_url' => $cancelUrl,
                    ]
                );
            }
        } catch (Throwable $exception) {
            Log::error('No se pudo crear la sesión de compra', [
                'user_id' => $user?->getKey(),
                'email' => $validated['email'] ?? null,
                'template_id' => $template->getKey(),
                'is_guest' => $isGuest,
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
