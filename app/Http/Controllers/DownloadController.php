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
            return response([
                'message' => 'No encontramos una licencia válida para esta descarga.',
            ], 403);
        }

        if (! $license->canDownload()) {
            return response([
                'message' => 'Esta licencia expiró o alcanzó su límite de descargas.',
            ], 403);
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

        $license->registerDownload();

        $fileName = $template->slug . '.' . pathinfo($template->download_path, PATHINFO_EXTENSION);
        $absolutePath = Storage::disk('local')->path($template->download_path);

        return response()->download($absolutePath, $fileName);
    }

    private function resolveLicense(Request $request, Template $template): ?DownloadLicense
    {
        $user = $request->user();

        if ($user) {
            $license = $this->licenseService->findValidLicense($user->getKey(), $template);

            if ($license) {
                return $license;
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
