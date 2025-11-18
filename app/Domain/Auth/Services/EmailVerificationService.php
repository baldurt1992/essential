<?php

namespace App\Domain\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailVerificationMail;

class EmailVerificationService
{
    /**
     * Genera un OTP de 6 dígitos y lo asigna al usuario
     */
    public function generateOtp(User $user): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10); // OTP válido por 10 minutos

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        Log::info('OTP generated for user', [
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => $expiresAt,
        ]);

        return $otp;
    }

    /**
     * Envía el OTP por correo electrónico
     */
    public function sendOtp(User $user): void
    {
        $otp = $this->generateOtp($user);

        try {
            // Refrescar el usuario sin relaciones para evitar problemas de serialización en la cola
            $user->refresh();
            $user->unsetRelation('roles');

            Mail::to($user->email)->send(new EmailVerificationMail($user, $otp));

            Log::info('OTP email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send OTP email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verifica el OTP proporcionado por el usuario
     */
    public function verifyOtp(User $user, string $otp): bool
    {
        if (! $user->otp || ! $user->otp_expires_at) {
            return false;
        }

        if (now()->isAfter($user->otp_expires_at)) {
            Log::warning('OTP expired', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return false;
        }

        if ($user->otp !== $otp) {
            Log::warning('Invalid OTP provided', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return false;
        }

        // OTP válido - limpiar y verificar email
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        Log::info('Email verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return true;
    }

    /**
     * Verifica si el usuario tiene un OTP válido pendiente
     */
    public function hasValidOtp(User $user): bool
    {
        if (! $user->otp || ! $user->otp_expires_at) {
            return false;
        }

        return now()->isBefore($user->otp_expires_at);
    }
}
