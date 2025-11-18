<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.store');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['auth', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

    // OTP Email Verification
    Route::post('/verify-otp', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
        ->middleware('guest')
        ->name('otp.verify');

    Route::post('/resend-otp', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'resend'])
        ->middleware('guest')
        ->name('otp.resend');

    // Password Recovery
    Route::post('/password/recovery', [\App\Http\Controllers\Auth\PasswordRecoveryController::class, 'sendRecoveryCode'])
        ->middleware('guest')
        ->name('password.recovery');

    Route::post('/password/verify-otp', [\App\Http\Controllers\Auth\PasswordRecoveryController::class, 'verifyOtp'])
        ->middleware('guest')
        ->name('password.verify-otp');

    Route::post('/password/reset', [\App\Http\Controllers\Auth\PasswordRecoveryController::class, 'resetPassword'])
        ->middleware('guest')
        ->name('password.reset');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});
