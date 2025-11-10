<?php

namespace App\Domain\Catalog\Services;

use App\Domain\Billing\Models\Subscription;
use App\Domain\Billing\Services\DownloadLicenseService;
use App\Domain\Catalog\Models\Template;
use App\Models\User;

class TemplateAccessService
{
    public function __construct(private readonly DownloadLicenseService $licenseService)
    {
    }

    public function userHasAccess(?User $user, Template $template): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->licenseService->findValidLicense($user->getKey(), $template)) {
            return true;
        }

        return Subscription::query()
            ->where('user_id', $user->getKey())
            ->active()
            ->exists();
    }
}
