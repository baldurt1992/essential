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
            Mail::to($request->user()->email)->send(
                new PurchaseCompletedMail($purchase->loadMissing(['template', 'user']))
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
}

