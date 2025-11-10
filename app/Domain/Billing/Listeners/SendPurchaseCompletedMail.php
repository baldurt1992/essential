<?php

namespace App\Domain\Billing\Listeners;

use App\Domain\Billing\Events\PurchaseCompleted;
use App\Mail\PurchaseCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Log;

class SendPurchaseCompletedMail implements ShouldQueue
{
    public function __construct(private readonly Mailer $mailer) {}

    public function handle(PurchaseCompleted $event): void
    {
        $purchase = $event->purchase;
        $license = $event->license;

        $this->mailer->to($purchase->user->email)->send(new PurchaseCompletedMail($license));

        Log::info('Purchase email sent', [
            'purchase_id' => $purchase->getKey(),
            'license_id' => $license->getKey(),
            'user_id' => $purchase->user_id,
        ]);
    }
}
