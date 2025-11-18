<?php

namespace App\Domain\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordRecoveryMail;
use Illuminate\Support\Facades\Hash;

class PasswordRecoveryService
{
    /**
     * Genera un OTP de 6 dígitos para recuperación de contraseña
     */
    public function generateRecoveryOtp(User $user): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10); // OTP válido por 10 minutos

        $user->update([
            'password_recovery_otp' => $otp,
            'password_recovery_otp_expires_at' => $expiresAt,
        ]);

        Log::info('Password recovery OTP generated for user', [
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => $expiresAt,
        ]);

        return $otp;
    }

    /**
     * Envía el OTP de recuperación por correo electrónico
     */
    public function sendRecoveryOtp(User $user): void
    {
        $otp = $this->generateRecoveryOtp($user);

        try {
            // Refrescar el usuario sin relaciones para evitar problemas de serialización en la cola
            $user->refresh();
            $user->unsetRelation('roles');

            Mail::to($user->email)->send(new PasswordRecoveryMail($user, $otp));

            Log::info('Password recovery OTP email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send password recovery OTP email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verifica el OTP de recuperación proporcionado por el usuario
     */
    public function verifyRecoveryOtp(User $user, string $otp): bool
    {
        if (! $user->password_recovery_otp || ! $user->password_recovery_otp_expires_at) {
            return false;
        }

        if (now()->isAfter($user->password_recovery_otp_expires_at)) {
            Log::warning('Password recovery OTP expired', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return false;
        }

        if ($user->password_recovery_otp !== $otp) {
            Log::warning('Invalid password recovery OTP provided', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return false;
        }

        // OTP válido - no limpiar todavía, se limpiará después de actualizar la contraseña
        Log::info('Password recovery OTP verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return true;
    }

    /**
     * Actualiza la contraseña del usuario y limpia el OTP
     */
    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
            'password_recovery_otp' => null,
            'password_recovery_otp_expires_at' => null,
        ]);

        Log::info('Password reset successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Verifica si el usuario tiene un OTP de recuperación válido pendiente
     */
    public function hasValidRecoveryOtp(User $user): bool
    {
        if (! $user->password_recovery_otp || ! $user->password_recovery_otp_expires_at) {
            return false;
        }

        return now()->isBefore($user->password_recovery_otp_expires_at);
    }
}
