<?php

namespace App\Http\Controllers;

use App\Domain\Billing\Models\DownloadLicense;
use App\Domain\Billing\Services\DownloadLicenseService;
use App\Domain\Catalog\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends BaseController
{
    public function __construct(private readonly DownloadLicenseService $licenseService) {}

    public function __invoke(Request $request, string $templateIdentifier): BinaryFileResponse|Response
    {
        $template = Template::query()
            ->where('slug', $templateIdentifier)
            ->orWhere('uuid', $templateIdentifier)
            ->firstOrFail();

        $license = $this->resolveLicense($request, $template);

        if (! $license) {
            // Verificar si el error es por límite de descargas alcanzado
            $limitInfo = $request->session()->get('download_limit_reached');
            $request->session()->forget('download_limit_reached');

            if ($limitInfo) {
                return response([
                    'message' => "Límite de descargas para el plan {$limitInfo['plan_name']} ({$limitInfo['download_limit']} descargas/mes) alcanzado. Espera al siguiente mes para seguir descargando o actualiza tu plan.",
                ], 403);
            }

            return response([
                'message' => 'No encontramos una licencia válida para esta descarga.',
            ], 403);
        }

        if (! $license->canDownload()) {
            return response([
                'message' => 'Esta licencia expiró o alcanzó su límite de descargas.',
            ], 403);
        }

        // Verificar si ya descargó esta plantilla en el mes actual (ANTES de verificar el límite)
        // Si ya la descargó, permitir la re-descarga aunque haya alcanzado el límite
        $alreadyDownloadedThisMonth = false;
        if ($license->source_type === \App\Domain\Billing\Models\Subscription::class) {
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

                // Verificar si la licencia actual ya tenía una descarga este mes
                $currentLicenseDownloadedThisMonth = $license->last_downloaded_at
                    && $license->last_downloaded_at->year === now()->year
                    && $license->last_downloaded_at->month === now()->month;

                // Verificar si hay otra licencia para esta plantilla que ya fue descargada este mes
                $otherLicenseDownloadedThisMonth = \App\Domain\Billing\Models\DownloadLicense::query()
                    ->where('source_type', \App\Domain\Billing\Models\Subscription::class)
                    ->where('source_id', $subscription->getKey())
                    ->where('template_id', $template->getKey())
                    ->whereNotNull('last_downloaded_at')
                    ->whereYear('last_downloaded_at', now()->year)
                    ->whereMonth('last_downloaded_at', now()->month)
                    ->where('id', '!=', $license->getKey())
                    ->exists();

                $alreadyDownloadedThisMonth = $currentLicenseDownloadedThisMonth || $otherLicenseDownloadedThisMonth;
            }
        }

        // Verificar límite de descargas mensual por suscripción SOLO si NO ha descargado esta plantilla este mes
        if ($license->source_type === \App\Domain\Billing\Models\Subscription::class && ! $alreadyDownloadedThisMonth) {
            $subscription = $license->source;
            if ($subscription) {
                // Asegurar que el plan esté cargado
                if (! $subscription->relationLoaded('plan')) {
                    $subscription->load('plan');
                }

                if ($subscription->plan) {
                    $plan = $subscription->plan;

                    // Si el plan tiene límite y no es ilimitado, verificar contador mensual
                    if (! $plan->unlimited_downloads && $plan->download_limit !== null) {
                        Log::info('Checking download limit in __invoke', [
                            'user_id' => $subscription->user_id,
                            'subscription_id' => $subscription->getKey(),
                            'template_id' => $template->getKey(),
                            'template_slug' => $template->slug,
                            'downloads_used' => $subscription->downloads_used,
                            'download_limit' => $plan->download_limit,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->getKey(),
                            'unlimited_downloads' => $plan->unlimited_downloads,
                        ]);

                        if ($subscription->downloads_used >= $plan->download_limit) {
                            Log::warning('Download limit reached in __invoke', [
                                'user_id' => $subscription->user_id,
                                'subscription_id' => $subscription->getKey(),
                                'template_id' => $template->getKey(),
                                'template_slug' => $template->slug,
                                'downloads_used' => $subscription->downloads_used,
                                'download_limit' => $plan->download_limit,
                                'plan_name' => $plan->name,
                                'plan_id' => $plan->getKey(),
                            ]);

                            return response([
                                'message' => "Límite de descargas para el plan {$plan->name} ({$plan->download_limit} descargas/mes) alcanzado. Espera al siguiente mes para seguir descargando o actualiza tu plan.",
                            ], 403);
                        }
                    }
                }
            }
        }

        if (! $template->download_path || ! Storage::disk('local')->exists($template->download_path)) {
            Log::error('Download file missing', [
                'template_id' => $template->getKey(),
                'path' => $template->download_path,
            ]);

            return response([
                'message' => 'El archivo no está disponible temporalmente. Inténtalo más tarde.',
            ], 503);
        }

        // Registrar descarga en la licencia
        $license->registerDownload();

        // Si la licencia viene de una suscripción, incrementar el contador de la suscripción
        if ($license->source_type === \App\Domain\Billing\Models\Subscription::class) {
            $subscription = $license->source;
            if ($subscription) {
                // Recargar la suscripción para asegurar que tenemos los datos actualizados
                $subscription->refresh();

                // Asegurar que el plan esté cargado
                if (! $subscription->relationLoaded('plan')) {
                    $subscription->load('plan');
                }

                if ($subscription->plan) {
                    $plan = $subscription->plan;

                    // Solo incrementar si el plan tiene límite (no es ilimitado)
                    if (! $plan->unlimited_downloads && $plan->download_limit !== null) {
                        // Solo incrementar si NO ha descargado esta plantilla en el mes actual
                        if (! $alreadyDownloadedThisMonth) {
                        // Usar increment para actualizar directamente en la BD
                        $subscription->increment('downloads_used');

                        // Recargar la suscripción después del incremento para tener el valor actualizado
                        $subscription->refresh();

                        Log::info('Download counter incremented', [
                            'user_id' => $subscription->user_id,
                            'subscription_id' => $subscription->getKey(),
                                'template_id' => $template->getKey(),
                                'template_slug' => $template->slug,
                            'downloads_used' => $subscription->downloads_used,
                            'download_limit' => $plan->download_limit,
                            'plan_name' => $plan->name,
                        ]);
                        } else {
                            Log::info('Download counter NOT incremented - template already downloaded this month', [
                                'user_id' => $subscription->user_id,
                                'subscription_id' => $subscription->getKey(),
                                'template_id' => $template->getKey(),
                                'template_slug' => $template->slug,
                                'license_id' => $license->getKey(),
                                'previous_last_downloaded_at' => $license->last_downloaded_at?->toDateString(),
                                'previous_download_count' => $license->download_count - 1,
                            ]);
                        }
                    }
                }
            }
        }

        $fileName = $template->slug . '.' . pathinfo($template->download_path, PATHINFO_EXTENSION);
        $absolutePath = Storage::disk('local')->path($template->download_path);

        return response()->download($absolutePath, $fileName);
    }

    /**
     * Resetea el contador de descargas si cambió el mes
     */
    private function resetMonthlyDownloadsIfNeeded(\App\Domain\Billing\Models\Subscription $subscription): void
    {
        $now = now();
        $resetAt = $subscription->downloads_reset_at;

        // Si no hay fecha de reset o cambió el mes, resetear
        if (! $resetAt || $resetAt->format('Y-m') !== $now->format('Y-m')) {
            $subscription->downloads_used = 0;
            $subscription->downloads_reset_at = $now->copy()->startOfMonth();
            $subscription->save();
        }
    }

    private function resolveLicense(Request $request, Template $template): ?DownloadLicense
    {
        $user = $request->user();

        if ($user) {
            $license = $this->licenseService->findValidLicense($user->getKey(), $template);

            if ($license) {
                return $license;
            }

            // Intentar crear nueva licencia por suscripción
            // Primero verificamos si tiene suscripción activa para dar mensaje más claro
            $subscription = \App\Domain\Billing\Models\Subscription::query()
                ->where('user_id', $user->getKey())
                ->active()
                ->with('plan')
                ->latest('current_period_end')
                ->first();

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

                    // Si el plan tiene límite y se alcanzó, guardar info para mensaje de error
                    if (! $plan->unlimited_downloads && $plan->download_limit !== null) {
                        Log::info('Checking download limit in resolveLicense', [
                            'user_id' => $user->getKey(),
                            'subscription_id' => $subscription->getKey(),
                            'downloads_used' => $subscription->downloads_used,
                            'download_limit' => $plan->download_limit,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->getKey(),
                        ]);

                        if ($subscription->downloads_used >= $plan->download_limit) {
                            Log::warning('Download limit reached in resolveLicense', [
                                'user_id' => $user->getKey(),
                                'subscription_id' => $subscription->getKey(),
                                'downloads_used' => $subscription->downloads_used,
                                'download_limit' => $plan->download_limit,
                                'plan_name' => $plan->name,
                                'plan_id' => $plan->getKey(),
                            ]);

                            // Guardar info en la sesión para el mensaje de error
                            $request->session()->put('download_limit_reached', [
                                'plan_name' => $plan->name,
                                'download_limit' => $plan->download_limit,
                            ]);
                            return null;
                        }
                    }
                }
            }

            if ($subscriptionLicense = $this->licenseService->issueForActiveSubscription($user, $template)) {
                return $subscriptionLicense;
            }
        }

        // Intentar con purchase_code (usuarios autenticados sin suscripción)
        $code = $request->string('code')->trim();

        if ($code->isNotEmpty()) {
            $license = $this->licenseService->findByPurchaseCode($code->toString(), $template);

            if ($license && $license->canDownload()) {
                return $license;
            }
        }

        // Intentar con token temporal (invitados)
        $token = $request->string('token')->trim();

        if ($token->isNotEmpty()) {
            Log::info('Attempting to find license by purchase token', [
                'token' => $token->toString(),
                'template_id' => $template->getKey(),
                'template_slug' => $template->slug,
            ]);

            $license = $this->licenseService->findByPurchaseToken($token->toString(), $template);

            if ($license) {
                Log::info('License found by purchase token', [
                    'license_id' => $license->getKey(),
                    'can_download' => $license->canDownload(),
                    'user_id' => $license->user_id,
                    'purchase_code' => $license->purchase_code,
                ]);

                if ($license->canDownload()) {
                    return $license;
                }
            } else {
                Log::warning('License not found by purchase token', [
                    'token' => $token->toString(),
                    'template_id' => $template->getKey(),
                ]);
            }
        }

        return null;
    }
}
