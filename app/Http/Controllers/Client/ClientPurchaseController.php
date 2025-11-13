<?php

namespace App\Http\Controllers\Client;

use App\Domain\Billing\Models\Purchase;
use App\Http\Controllers\Controller;
use App\Mail\PurchaseCompletedMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ClientPurchaseController extends Controller
{
    public function resendCode(Request $request, Purchase $purchase): JsonResponse
    {
        // Verificar que la compra pertenece al usuario autenticado
        if ($purchase->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta compra.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (! $purchase->purchase_code) {
            return response()->json([
                'message' => 'Esta compra no tiene un código de compra asociado.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $license = $purchase->downloadLicense ?? app(\App\Domain\Billing\Services\DownloadLicenseService::class)
                ->findByPurchaseCode($purchase->purchase_code, $purchase->template);

            if (! $license) {
                return response()->json([
                    'message' => 'No se encontró la licencia asociada a esta compra.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            Mail::to($request->user()->email)->send(
                new PurchaseCompletedMail($license, $purchase->loadMissing(['template', 'user']))
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No pudimos enviar el correo. Intenta nuevamente más tarde.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'El código de compra ha sido reenviado a tu correo electrónico.',
        ]);
    }

    public function validateCode(Request $request, Purchase $purchase): JsonResponse
    {
        // Verificar que la compra pertenece al usuario autenticado
        if ($purchase->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta compra.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'min:12', 'max:14'], // Formato: XXXX-XXXX-XXXX o XXXXXXXXXXXX
        ]);

        // Normalizar el código: remover guiones y espacios, convertir a mayúsculas
        $code = strtoupper(str_replace([' ', '-'], '', $validated['code']));

        // Validar que tenga exactamente 12 caracteres alfanuméricos
        if (strlen($code) !== 12 || !preg_match('/^[A-Z0-9]{12}$/', $code)) {
            return response()->json([
                'message' => 'El formato del código no es válido. Debe ser XXXX-XXXX-XXXX.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $formattedCode = substr($code, 0, 4) . '-' . substr($code, 4, 4) . '-' . substr($code, 8, 4);

        if ($purchase->purchase_code !== $formattedCode) {
            return response()->json([
                'message' => 'El código de compra no es válido.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // El código es válido, retornar URL de descarga
        $downloadUrl = route('downloads.show', [
            'template' => $purchase->template->slug,
            'code' => $purchase->purchase_code,
        ]);

        return response()->json([
            'message' => 'Código válido',
            'download_url' => $downloadUrl,
        ]);
    }
}
