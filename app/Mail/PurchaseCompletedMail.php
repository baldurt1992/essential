<?php

namespace App\Mail;

use App\Domain\Billing\Models\DownloadLicense;
use App\Domain\Billing\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PurchaseCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public DownloadLicense $license,
        public Purchase $purchase
    ) {}

    public function build(): self
    {
        $isGuest = ! $this->purchase->user_id;
        $downloadUrl = $isGuest ? $this->guestDownloadUrl() : $this->authenticatedDownloadUrl();

        return $this
            ->subject('Tu compra está lista: ' . $this->license->template->title)
            ->markdown('emails.purchase-completed', [
                'license' => $this->license,
                'template' => $this->license->template,
                'user' => $this->license->user ?? null,
                'purchase' => $this->purchase,
                'isGuest' => $isGuest,
                'purchaseCode' => $this->purchase->purchase_code,
                'downloadUrl' => $downloadUrl,
            ]);
    }

    private function authenticatedDownloadUrl(): ?string
    {
        // Para usuarios autenticados, no enviamos link directo
        // Deben usar el purchase_code en el panel
        return null;
    }

    private function guestDownloadUrl(): string
    {
        // Para invitados, generamos link temporal firmado usando el UUID de la purchase
        return URL::temporarySignedRoute(
            'downloads.show',
            now()->addDays(30),
            [
                'template' => $this->license->template->slug,
                'token' => $this->purchase->uuid,
            ],
        );
    }
}
