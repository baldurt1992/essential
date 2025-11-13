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

    public function issueForGuestPurchase(Purchase $purchase, ?int $downloadLimit = 5, ?Carbon $expiresAt = null): DownloadLicense
    {
        // Para invitados, no generamos purchase_code, solo creamos la licencia
        // El link de descarga será temporal y firmado
        $expiresAt = $expiresAt ?? now()->addDays(30); // 30 días por defecto

        $license = DownloadLicense::updateOrCreate(
            [
                'source_type' => Purchase::class,
                'source_id' => $purchase->getKey(),
                'template_id' => $purchase->template_id,
            ],
            [
                'user_id' => null, // Invitados no tienen user_id
                'purchase_code' => null, // Invitados no usan purchase_code
                'download_limit' => $downloadLimit,
                'expires_at' => $expiresAt,
            ],
        );

        // Asegurar que purchase_code sea null (por si ya existía una licencia con código)
        if ($license->purchase_code !== null) {
            $license->purchase_code = null;
            $license->save();
        }

        return $license;
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
        $licenses = DownloadLicense::query()
            ->where('user_id', $userId)
            ->where('template_id', $template->getKey())
            ->with(['source' => function ($query) {
                if ($query->getModel() instanceof Subscription) {
                    $query->with('plan');
                }
            }])
            ->get();

        return $licenses->first(function (DownloadLicense $license) {
            if (! $license->canDownload()) {
                return false;
            }

            // Si la licencia viene de una suscripción, verificar límite de descargas mensual
            if ($license->source_type === Subscription::class) {
                $subscription = $license->source;
                if ($subscription) {
                    // Resetear contador mensual si es necesario (esto actualiza la BD)
                    $this->resetMonthlyDownloadsIfNeeded($subscription);

                    // Recargar la suscripción desde la BD para obtener el downloads_used actualizado
                    $subscription->refresh();

                    // Asegurar que el plan esté cargado
                    if (! $subscription->relationLoaded('plan')) {
                        $subscription->load('plan');
                    }

                    if ($subscription->plan) {
                        $plan = $subscription->plan;

                        // Si el plan tiene límite y no es ilimitado, verificar contador mensual
                        if (! $plan->unlimited_downloads && $plan->download_limit !== null) {
                            if ($subscription->downloads_used >= $plan->download_limit) {
                                \Illuminate\Support\Facades\Log::info('Download limit reached in findValidLicense', [
                                    'user_id' => $subscription->user_id,
                                    'subscription_id' => $subscription->getKey(),
                                    'downloads_used' => $subscription->downloads_used,
                                    'download_limit' => $plan->download_limit,
                                    'plan_name' => $plan->name,
                                    'plan_id' => $plan->getKey(),
                                ]);
                                return false; // Ya alcanzó el límite mensual
                            }
                        }
                    }
                }
            }

            return true;
        });
    }

    public function findByPurchaseCode(string $code, Template $template): ?DownloadLicense
    {
        return DownloadLicense::query()
            ->where('purchase_code', $code)
            ->where('template_id', $template->getKey())
            ->first();
    }

    public function findByPurchaseToken(string $purchaseUuid, Template $template): ?DownloadLicense
    {
        // Buscar la Purchase por UUID
        $purchase = \App\Domain\Billing\Models\Purchase::where('uuid', $purchaseUuid)->first();

        if (! $purchase) {
            \Illuminate\Support\Facades\Log::warning('Purchase not found by UUID', [
                'uuid' => $purchaseUuid,
                'template_id' => $template->getKey(),
            ]);
            return null;
        }

        \Illuminate\Support\Facades\Log::info('Purchase found, searching for license', [
            'purchase_id' => $purchase->getKey(),
            'template_id' => $template->getKey(),
            'purchase_user_id' => $purchase->user_id,
            'purchase_guest_email' => $purchase->guest_email,
        ]);

        // Buscar la licencia asociada a esta Purchase
        $license = DownloadLicense::query()
            ->where('source_type', \App\Domain\Billing\Models\Purchase::class)
            ->where('source_id', $purchase->getKey())
            ->where('template_id', $template->getKey())
            ->whereNull('user_id') // Solo para invitados
            ->whereNull('purchase_code') // Invitados no tienen purchase_code
            ->first();

        if (! $license) {
            \Illuminate\Support\Facades\Log::warning('License not found for purchase', [
                'purchase_id' => $purchase->getKey(),
                'template_id' => $template->getKey(),
                'available_licenses' => DownloadLicense::query()
                    ->where('source_type', \App\Domain\Billing\Models\Purchase::class)
                    ->where('source_id', $purchase->getKey())
                    ->where('template_id', $template->getKey())
                    ->get(['id', 'user_id', 'purchase_code'])
                    ->toArray(),
            ]);
        }

        return $license;
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
            ->with('plan')
            ->latest('current_period_end')
            ->first();

        if (! $subscription) {
            return null;
        }

        // Resetear contador mensual si es necesario (esto actualiza la BD)
        $this->resetMonthlyDownloadsIfNeeded($subscription);

        // Recargar la suscripción desde la BD para obtener el downloads_used actualizado
        $subscription->refresh();

        // Asegurar que el plan esté cargado
        if (! $subscription->relationLoaded('plan')) {
            $subscription->load('plan');
        }

        $plan = $subscription->plan;

        // Verificar límite de descargas si el plan tiene límite
        if ($plan && ! $plan->unlimited_downloads && $plan->download_limit !== null) {
            // Si el usuario ya alcanzó el límite mensual, no permitir más descargas
            if ($subscription->downloads_used >= $plan->download_limit) {
                \Illuminate\Support\Facades\Log::info('Download limit reached in issueForActiveSubscription', [
                    'user_id' => $user->getKey(),
                    'subscription_id' => $subscription->getKey(),
                    'downloads_used' => $subscription->downloads_used,
                    'download_limit' => $plan->download_limit,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->getKey(),
                ]);
                return null;
            }
        }

        return $this->issueForSubscription($subscription, $template);
    }

    /**
     * Resetea el contador de descargas si cambió el mes
     */
    private function resetMonthlyDownloadsIfNeeded(Subscription $subscription): void
    {
        $now = now();
        $resetAt = $subscription->downloads_reset_at;

        // Si no hay fecha de reset o cambió el mes, resetear
        if (! $resetAt || $resetAt->format('Y-m') !== $now->format('Y-m')) {
            $subscription->downloads_used = 0;
            $subscription->downloads_reset_at = $now->startOfMonth();
            $subscription->save();
        }
    }
}
