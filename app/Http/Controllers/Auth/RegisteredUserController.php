<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Services\EmailVerificationService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly EmailVerificationService $emailVerificationService
    ) {}

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        $user->assignRole('client');

        // Refresh to ensure roles are loaded
        $user->refresh();
        $user->load('roles');

        event(new Registered($user));

        // Enviar OTP en lugar de iniciar sesión automáticamente
        $this->emailVerificationService->sendOtp($user);

        return response()->json([
            'message' => 'Registro exitoso. Por favor verifica tu correo electrónico.',
            'email' => $user->email,
        ], 201);
    }
}
