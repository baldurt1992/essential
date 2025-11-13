<?php

namespace App\Http\Controllers\Site;

use App\Domain\Billing\Models\Purchase;
use App\Domain\Settings\Services\ContactInformationService;
use App\Http\Controllers\Controller;
use App\Mail\PurchaseCompletedMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function __construct(private readonly ContactInformationService $contactInformationService) {}

    /**
     * Verificar una compra por session_id de Stripe (público, para invitados y autenticados)
     */
    public function verify(string $sessionId): JsonResponse
    {
        $purchase = Purchase::where('stripe_session_id', $sessionId)
            ->with(['template'])
            ->first();

        if (! $purchase) {
            return response()->json([
                'message' => 'No se encontró una compra asociada a esta sesión.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Obtener información de contacto para mostrar el email de soporte
        $contactInfo = $this->contactInformationService->get();

        // Retornar información básica de la compra (sin datos sensibles)
        return response()->json([
            'purchase' => [
                'uuid' => $purchase->uuid,
                'user_id' => $purchase->user_id,
                'guest_email' => $purchase->guest_email,
                'template' => [
                    'slug' => $purchase->template->slug,
                    'title' => $purchase->template->title,
                ],
                'status' => $purchase->status,
                'purchased_at' => $purchase->purchased_at,
            ],
            'contact_email' => $contactInfo->email,
        ]);
    }

    /**
     * Reenviar enlace de descarga para compras de invitados (público)
     */
    public function resendLink(Request $request, Purchase $purchase): JsonResponse
    {
        // Solo permitir para compras de invitados
        if ($purchase->user_id !== null) {
            return response()->json([
                'message' => 'Esta funcionalidad solo está disponible para compras de invitados.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (! $purchase->guest_email) {
            return response()->json([
                'message' => 'Esta compra no tiene un correo electrónico asociado.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Validar que el email proporcionado coincida con el de la compra
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        if ($validated['email'] !== $purchase->guest_email) {
            return response()->json([
                'message' => 'El correo electrónico no coincide con el de la compra.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $licenseService = app(\App\Domain\Billing\Services\DownloadLicenseService::class);
            $license = $licenseService->findByPurchaseToken($purchase->uuid, $purchase->template);

            if (! $license) {
                return response()->json([
                    'message' => 'No se encontró la licencia asociada a esta compra.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            Mail::to($purchase->guest_email)->send(
                new PurchaseCompletedMail($license, $purchase->loadMissing(['template']))
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No pudimos enviar el correo. Intenta nuevamente más tarde.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'El enlace de descarga ha sido reenviado a tu correo electrónico.',
        ]);
    }
}

