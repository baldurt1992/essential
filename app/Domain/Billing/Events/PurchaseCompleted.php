<?php

namespace App\Domain\Billing\Events;

use App\Domain\Billing\Models\DownloadLicense;
use App\Domain\Billing\Models\Purchase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseCompleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Purchase $purchase,
        public DownloadLicense $license,
    ) {}
}
