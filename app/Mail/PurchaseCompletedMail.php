<?php

namespace App\Mail;

use App\Domain\Billing\Models\DownloadLicense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PurchaseCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public DownloadLicense $license)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Tu compra está lista: '.$this->license->template->title)
            ->markdown('emails.purchase-completed', [
                'license' => $this->license,
                'template' => $this->license->template,
                'user' => $this->license->user,
                'downloadUrl' => $this->downloadUrl(),
            ]);
    }

    private function downloadUrl(): string
    {
        return URL::temporarySignedRoute(
            'downloads.show',
            now()->addHours(24),
            [
                'template' => $this->license->template->slug,
                'code' => $this->license->purchase_code,
            ],
        );
    }
}
