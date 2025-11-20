<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        $this->ensureStorageSymlink();
        $this->configurePhpLimits();
    }

    private function configurePhpLimits(): void
    {
        // Configurar límites de PHP para archivos grandes (solo en local)
        // NOTA: upload_max_filesize y post_max_size son PHP_INI_SYSTEM y no se pueden cambiar en runtime
        // Estos deben configurarse en php.ini, .htaccess o .user.ini
        if (app()->environment('local')) {
            @ini_set('max_execution_time', '600');
            @ini_set('max_input_time', '600');
            @ini_set('memory_limit', '256M');
        }
    }

    private function ensureStorageSymlink(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $filesystem = app(Filesystem::class);
        $storagePath = storage_path('app/public');
        $publicPath = public_path('storage');

        if (! $filesystem->exists($storagePath)) {
            return;
        }

        if (is_link($publicPath) || $filesystem->exists($publicPath)) {
            return;
        }

        try {
            $filesystem->link($storagePath, $publicPath);
        } catch (\Throwable $exception) {
            Log::error('Failed to create storage symlink', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
