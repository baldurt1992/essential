<?php

namespace App\Domain\Billing\Listeners;

use App\Domain\Billing\Events\PurchaseCompleted;
use App\Mail\PurchaseCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Log;

class SendPurchaseCompletedMail // implements ShouldQueue // Temporalmente desactivado para debugging
{
    public function __construct(private readonly Mailer $mailer) {}

    public function handle(PurchaseCompleted $event): void
    {
        $purchase = $event->purchase;
        $license = $event->license;

        // Determinar el email del destinatario
        $recipientEmail = $purchase->user_id
            ? $purchase->user->email
            : $purchase->guest_email;

        if (! $recipientEmail) {
            Log::warning('Purchase completed without recipient email', [
                'purchase_id' => $purchase->getKey(),
                'license_id' => $license->getKey(),
            ]);

            return;
        }

        try {
            Log::info('Attempting to send purchase completed email', [
                'purchase_id' => $purchase->getKey(),
                'license_id' => $license->getKey(),
                'recipient_email' => $recipientEmail,
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'from_address' => config('mail.from.address'),
                ],
            ]);

            $this->mailer->to($recipientEmail)->send(new PurchaseCompletedMail($license, $purchase));

            Log::info('Purchase email sent successfully', [
                'purchase_id' => $purchase->getKey(),
                'license_id' => $license->getKey(),
                'user_id' => $purchase->user_id,
                'guest_email' => $purchase->guest_email,
                'recipient_email' => $recipientEmail,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send purchase completed email', [
                'purchase_id' => $purchase->getKey(),
                'license_id' => $license->getKey(),
                'recipient_email' => $recipientEmail,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            // No lanzar la excepción para que el webhook no falle
            // El correo se puede reenviar manualmente si es necesario
        }
    }
}
