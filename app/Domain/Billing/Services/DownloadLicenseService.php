<?php

namespace App\Domain\Billing\Services;

use App\Domain\Billing\Models\DownloadLicense;
use App\Domain\Billing\Models\Purchase;
use App\Domain\Billing\Models\Subscription;
use App\Domain\Catalog\Models\Template;
use App\Models\User;
use Illuminate\Support\Carbon;

class DownloadLicenseService
{
    public function issueForPurchase(Purchase $purchase, ?int $downloadLimit = null, ?Carbon $expiresAt = null): DownloadLicense
    {
        return DownloadLicense::updateOrCreate(
            [
                'source_type' => Purchase::class,
                'source_id' => $purchase->getKey(),
                'template_id' => $purchase->template_id,
            ],
            [
                'user_id' => $purchase->user_id,
                'purchase_code' => $purchase->purchase_code,
                'download_limit' => $downloadLimit,
                'expires_at' => $expiresAt,
            ],
        );
    }

    public function issueForSubscription(Subscription $subscription, Template $template): DownloadLicense
    {
        return DownloadLicense::updateOrCreate(
            [
                'source_type' => Subscription::class,
                'source_id' => $subscription->getKey(),
                'template_id' => $template->getKey(),
            ],
            [
                'user_id' => $subscription->user_id,
                'download_limit' => null,
                'expires_at' => $subscription->ends_at,
                'purchase_code' => null,
            ],
        );
    }

    public function findValidLicense(int $userId, Template $template): ?DownloadLicense
    {
        return DownloadLicense::query()
            ->where('user_id', $userId)
            ->where('template_id', $template->getKey())
            ->get()
            ->first(fn(DownloadLicense $license) => $license->canDownload());
    }

    public function findByPurchaseCode(string $code, Template $template): ?DownloadLicense
    {
        return DownloadLicense::query()
            ->where('purchase_code', $code)
            ->where('template_id', $template->getKey())
            ->first();
    }

    public function ensureCanDownload(DownloadLicense $license): bool
    {
        return $license->canDownload();
    }

    public function issueForActiveSubscription(User $user, Template $template): ?DownloadLicense
    {
        $subscription = Subscription::query()
            ->where('user_id', $user->getKey())
            ->active()
            ->latest('current_period_end')
            ->first();

        if (! $subscription) {
            return null;
        }

        return $this->issueForSubscription($subscription, $template);
    }
}
