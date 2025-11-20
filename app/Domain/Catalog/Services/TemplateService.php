<?php

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Template;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class TemplateService
{
    public function __construct(private readonly StripeClient $stripe) {}

    public function store(User $user, array $data): Template
    {
        $template = new Template();
        $template->fill($this->mapStoreData($data));
        $template->created_by = $user->getKey();

        $this->handleUploads($template, $data);

        $template->save();

        $this->syncStripe($template, (float) $data['price']);

        return $template->refresh();
    }

    public function update(Template $template, array $data): Template
    {
        $originalPrice = $template->price_cents;

        $template->fill($this->mapUpdateData($template, $data));
        $this->handleUploads($template, $data);
        $template->save();

        if (array_key_exists('price', $data) && $this->priceChanged($originalPrice, (float) $data['price'])) {
            $this->syncStripe($template, (float) $data['price'], regeneratePrice: true);
        }

        return $template->refresh();
    }

    public function delete(Template $template): void
    {
        $this->deleteFile($template->preview_image_path, 'public');
        $this->deleteFile($template->download_path, 'local');

        $template->delete();
    }

    public function deletePackageFile(Template $template): void
    {
        Log::info('[TemplateService] Deleting package file', [
            'template_id' => $template->id,
            'download_path' => $template->download_path,
        ]);

        $this->deleteFile($template->download_path, 'local');
        $template->download_path = null;
        $template->save();

        Log::info('[TemplateService] Package file deleted', [
            'template_id' => $template->id,
        ]);
    }

    private function mapStoreData(array $data): array
    {
        return [
            'title' => $data['title'],
            'slug' => $data['slug'] ?? null,
            'description' => $data['description'] ?? null,
            'price_cents' => $this->toCents((float) $data['price']),
            'currency' => config('billing.currency', 'eur'),
            'tags' => Arr::get($data, 'tags', []),
            'metadata' => Arr::get($data, 'metadata', []),
            'is_active' => Arr::get($data, 'is_active', true),
            'is_popular' => Arr::get($data, 'is_popular', false),
            'is_new' => Arr::get($data, 'is_new', false),
            'sort_order' => Arr::get($data, 'sort_order', 0),
        ];
    }

    private function mapUpdateData(Template $template, array $data): array
    {
        $payload = [];

        if (array_key_exists('title', $data)) {
            $payload['title'] = $data['title'];
        }

        if (array_key_exists('slug', $data)) {
            $payload['slug'] = $data['slug'];
        }

        if (array_key_exists('description', $data)) {
            $payload['description'] = $data['description'];
        }

        if (array_key_exists('price', $data)) {
            $payload['price_cents'] = $this->toCents((float) $data['price']);
        }

        if (array_key_exists('tags', $data)) {
            $payload['tags'] = Arr::get($data, 'tags', []);
        }

        if (array_key_exists('metadata', $data)) {
            $payload['metadata'] = Arr::get($data, 'metadata', $template->metadata ?? []);
        }

        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (bool) $data['is_active'];
        }

        if (array_key_exists('is_popular', $data)) {
            $payload['is_popular'] = (bool) $data['is_popular'];
        }

        if (array_key_exists('is_new', $data)) {
            $payload['is_new'] = (bool) $data['is_new'];
        }

        if (array_key_exists('sort_order', $data)) {
            $payload['sort_order'] = (int) $data['sort_order'];
        }

        return $payload;
    }

    private function handleUploads(Template $template, array $data): void
    {
        if ($preview = Arr::get($data, 'preview_image')) {
            Log::info('Handling preview image upload', [
                'template_id' => $template->id,
                'file_name' => $preview->getClientOriginalName(),
                'file_size' => $preview->getSize(),
                'mime_type' => $preview->getMimeType(),
            ]);

            // Asegurar que el directorio existe
            $previewDir = 'templates/previews';
            // Usar 'public_storage' para guardar directamente en public/storage/
            $publicStorageDisk = Storage::disk('public_storage');
            if (! $publicStorageDisk->exists($previewDir)) {
                Log::info('Creating preview directory', ['directory' => $previewDir]);
                $publicStorageDisk->makeDirectory($previewDir, 0755, true);
            }

            // Eliminar archivo antiguo (verificar en ambos discos por compatibilidad)
            $this->deleteFile($template->preview_image_path, 'public');
            $this->deleteFile($template->preview_image_path, 'public_storage');

            $storedPath = $this->storeFile($preview, $previewDir, 'public_storage');

            Log::info('Preview image stored', [
                'template_id' => $template->id,
                'stored_path' => $storedPath,
                'file_exists' => $publicStorageDisk->exists($storedPath),
                'full_path' => $publicStorageDisk->path($storedPath),
            ]);

            $template->preview_image_path = $storedPath;
        }

        if ($package = Arr::get($data, 'package_file')) {
            Log::info('[TemplateService] Handling package file upload', [
                'template_id' => $template->id,
                'file_name' => $package->getClientOriginalName(),
                'file_size' => $package->getSize(),
                'file_size_mb' => round($package->getSize() / (1024 * 1024), 2),
                'is_valid' => $package->isValid(),
                'current_download_path' => $template->download_path,
            ]);

            // Verificar que el archivo sea válido antes de procesarlo
            if (!$package->isValid()) {
                Log::error('[TemplateService] Package file is invalid', [
                    'template_id' => $template->id,
                    'error' => $package->getError(),
                    'error_message' => $package->getErrorMessage(),
                ]);
                throw new \RuntimeException('El archivo no es válido: ' . $package->getErrorMessage());
            }

            // Asegurar que el directorio existe
            $packageDir = 'templates/packages';
            $localDisk = Storage::disk('local');
            if (! $localDisk->exists($packageDir)) {
                Log::info('[TemplateService] Creating package directory', ['directory' => $packageDir]);
                $localDisk->makeDirectory($packageDir, 0755, true);
            }

            // Eliminar el archivo anterior si existe
            if ($template->download_path) {
                Log::info('[TemplateService] Deleting old package file before upload', [
                    'old_path' => $template->download_path,
                ]);
                $this->deleteFile($template->download_path, 'local');
            }

            // Guardar el nuevo archivo
            $storedPath = $this->storeFile($package, $packageDir, 'local');

            // Verificar que el archivo se guardó correctamente
            if (!$localDisk->exists($storedPath)) {
                Log::error('[TemplateService] Package file was not stored', [
                    'template_id' => $template->id,
                    'stored_path' => $storedPath,
                ]);
                throw new \RuntimeException('No se pudo guardar el archivo en el servidor.');
            }

            $storedFileSize = $localDisk->size($storedPath);
            $originalFileSize = $package->getSize();

            // Verificar que el tamaño del archivo guardado coincida con el original
            if ($storedFileSize !== $originalFileSize) {
                Log::error('[TemplateService] Package file size mismatch', [
                    'template_id' => $template->id,
                    'original_size' => $originalFileSize,
                    'stored_size' => $storedFileSize,
                    'stored_path' => $storedPath,
                ]);
                // Eliminar el archivo incompleto
                $localDisk->delete($storedPath);
                throw new \RuntimeException('El archivo se guardó incompleto. Tamaño original: ' . round($originalFileSize / (1024 * 1024), 2) . 'MB, guardado: ' . round($storedFileSize / (1024 * 1024), 2) . 'MB');
            }

            Log::info('[TemplateService] Package file stored successfully', [
                'template_id' => $template->id,
                'stored_path' => $storedPath,
                'file_exists' => $localDisk->exists($storedPath),
                'file_size' => $storedFileSize,
                'file_size_mb' => round($storedFileSize / (1024 * 1024), 2),
            ]);

            // Actualizar el path en el modelo
            $template->download_path = $storedPath;
        }
    }

    private function syncStripe(Template $template, float $price, bool $regeneratePrice = false): void
    {
        if (! $template->stripe_product_id) {
            $product = $this->stripe->products->create([
                'name' => $template->title,
                'description' => $template->description,
                'metadata' => [
                    'type' => 'template_asset',
                    'template_uuid' => $template->uuid ?? Str::uuid()->toString(),
                ],
            ]);

            $template->stripe_product_id = $product->id;
            $template->save();
        }

        if ($regeneratePrice || ! $template->stripe_price_id) {
            $price = $this->stripe->prices->create([
                'unit_amount' => $this->toCents($price),
                'currency' => config('billing.currency', 'eur'),
                'product' => $template->stripe_product_id,
            ]);

            $template->stripe_price_id = $price->id;
            $template->save();
        }
    }

    private function priceChanged(int $originalPriceCents, float $newPrice): bool
    {
        return $originalPriceCents !== $this->toCents($newPrice);
    }

    private function storeFile(UploadedFile $file, string $directory, string $disk): string
    {
        $storage = Storage::disk($disk);

        // Obtener el nombre original del archivo y sanitizarlo
        $originalName = $file->getClientOriginalName();
        $sanitizedName = $this->sanitizeFileName($originalName);

        // Si el archivo ya existe, agregar un sufijo numérico para evitar colisiones
        $finalName = $this->ensureUniqueFileName($storage, $directory, $sanitizedName);

        // Guardar con el nombre original sanitizado
        $path = $file->storeAs($directory, $finalName, ['disk' => $disk]);

        Log::info('[TemplateService] File stored with original name', [
            'original_name' => $originalName,
            'sanitized_name' => $sanitizedName,
            'final_name' => $finalName,
            'directory' => $directory,
            'disk' => $disk,
            'stored_path' => $path,
            'full_path' => $storage->path($path),
            'file_exists' => $storage->exists($path),
            'file_size' => $storage->exists($path) ? $storage->size($path) : null,
        ]);

        return $path;
    }

    /**
     * Sanitiza el nombre del archivo para evitar problemas de seguridad
     * Mantiene el nombre legible pero elimina caracteres peligrosos
     */
    private function sanitizeFileName(string $fileName): string
    {
        // Obtener nombre y extensión
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $name = pathinfo($fileName, PATHINFO_FILENAME);

        // Sanitizar el nombre: remover caracteres peligrosos, normalizar espacios
        $sanitized = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $name);
        $sanitized = preg_replace('/\s+/', '_', trim($sanitized));

        // Si quedó vacío, usar un nombre por defecto
        if (empty($sanitized)) {
            $sanitized = 'file';
        }

        // Reconstruir con la extensión
        return $extension ? "{$sanitized}.{$extension}" : $sanitized;
    }

    /**
     * Asegura que el nombre del archivo sea único en el directorio
     * Si existe, agrega un sufijo numérico
     * 
     * @param mixed $storage Instancia de Storage disk
     * @param string $directory Directorio donde se guardará
     * @param string $fileName Nombre del archivo a verificar
     * @return string Nombre único del archivo
     */
    private function ensureUniqueFileName($storage, string $directory, string $fileName): string
    {
        $path = $directory . '/' . $fileName;

        if (!$storage->exists($path)) {
            return $fileName;
        }

        // Si existe, agregar sufijo numérico
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $name = pathinfo($fileName, PATHINFO_FILENAME);

        $counter = 1;
        do {
            $newFileName = $extension
                ? "{$name}_{$counter}.{$extension}"
                : "{$name}_{$counter}";
            $path = $directory . '/' . $newFileName;
            $counter++;
        } while ($storage->exists($path) && $counter < 1000);

        Log::info('[TemplateService] Generated unique file name', [
            'original' => $fileName,
            'unique' => $newFileName,
            'counter' => $counter - 1,
        ]);

        return $newFileName;
    }

    private function deleteFile(?string $path, string $disk): void
    {
        if (! $path) {
            return;
        }

        // Normalizar el path antes de verificar
        $normalizedPath = ltrim($path, '/');
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, 8);
        }

        $storage = Storage::disk($disk);
        if ($storage->exists($normalizedPath)) {
            $storage->delete($normalizedPath);
            Log::info('File deleted', [
                'path' => $normalizedPath,
                'disk' => $disk,
            ]);
        }
    }

    private function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
