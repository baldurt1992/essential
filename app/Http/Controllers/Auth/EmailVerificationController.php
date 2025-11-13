<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Services\EmailVerificationService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    public function __construct(
        private readonly EmailVerificationService $emailVerificationService
    ) {
    }

    /**
     * Verifica el OTP proporcionado por el usuario
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No encontramos una cuenta con este correo electrónico.'],
            ]);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Tu correo electrónico ya está verificado.',
                'verified' => true,
            ], 200);
        }

        $isValid = $this->emailVerificationService->verifyOtp($user, $request->otp);

        if (! $isValid) {
            throw ValidationException::withMessages([
                'otp' => ['El código de verificación es inválido o ha expirado.'],
            ]);
        }

        // Iniciar sesión automáticamente después de verificar
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        $user->refresh();
        $user->load('roles');

        return response()->json([
            'message' => 'Correo electrónico verificado exitosamente.',
            'user' => $user,
            'verified' => true,
        ], 200);
    }

    /**
     * Reenvía el OTP al usuario
     */
    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No encontramos una cuenta con este correo electrónico.'],
            ]);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Tu correo electrónico ya está verificado.',
                'verified' => true,
            ], 200);
        }

        $this->emailVerificationService->sendOtp($user);

        return response()->json([
            'message' => 'Hemos enviado un nuevo código de verificación a tu correo electrónico.',
        ], 200);
    }
}
