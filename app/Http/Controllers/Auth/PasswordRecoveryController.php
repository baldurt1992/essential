<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Services\PasswordRecoveryService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PasswordRecoveryController extends Controller
{
    public function __construct(
        private readonly PasswordRecoveryService $passwordRecoveryService
    ) {
    }

    /**
     * Envía el código OTP de recuperación al email del usuario
     */
    public function sendRecoveryCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No encontramos una cuenta con este correo electrónico.'],
            ]);
        }

        $this->passwordRecoveryService->sendRecoveryOtp($user);

        return response()->json([
            'message' => 'Hemos enviado un código de recuperación a tu correo electrónico.',
        ], 200);
    }

    /**
     * Verifica el OTP de recuperación proporcionado por el usuario
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No encontramos una cuenta con este correo electrónico.'],
            ]);
        }

        $isValid = $this->passwordRecoveryService->verifyRecoveryOtp($user, $request->otp);

        if (! $isValid) {
            throw ValidationException::withMessages([
                'otp' => ['El código de verificación es inválido o ha expirado.'],
            ]);
        }

        return response()->json([
            'message' => 'Código verificado exitosamente.',
            'verified' => true,
        ], 200);
    }

    /**
     * Actualiza la contraseña del usuario
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No encontramos una cuenta con este correo electrónico.'],
            ]);
        }

        // Verificar OTP antes de actualizar la contraseña
        $isValid = $this->passwordRecoveryService->verifyRecoveryOtp($user, $request->otp);

        if (! $isValid) {
            throw ValidationException::withMessages([
                'otp' => ['El código de verificación es inválido o ha expirado.'],
            ]);
        }

        $this->passwordRecoveryService->resetPassword($user, $request->password);

        return response()->json([
            'message' => 'Tu contraseña ha sido actualizada exitosamente.',
        ], 200);
    }
}

