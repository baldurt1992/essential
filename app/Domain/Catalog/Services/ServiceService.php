<?php

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceService
{
    public function store(User $user, array $data): Service
    {
        $service = new Service();
        $service->fill($this->mapStoreData($data));
        $service->created_by = $user->getKey();

        $this->handleUploads($service, $data);
        $service->save();

        return $service->refresh();
    }

    public function update(Service $service, array $data): Service
    {
        $service->fill($this->mapUpdateData($service, $data));
        $this->handleUploads($service, $data);
        $service->save();

        return $service->refresh();
    }

    public function delete(Service $service): void
    {
        // Eliminar archivo de ambos discos por compatibilidad
        $this->deleteFile($service->image_path, 'public');
        $this->deleteFile($service->image_path, 'public_storage');
        $service->delete();
    }

    private function mapStoreData(array $data): array
    {
        return [
            'title' => $data['title'],
            'slug' => $data['slug'] ?? null,
            'description' => $data['description'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'is_active' => Arr::get($data, 'is_active', true),
            'is_popular' => Arr::get($data, 'is_popular', false),
            'sort_order' => Arr::get($data, 'sort_order', 0),
            'metadata' => Arr::get($data, 'metadata', []),
        ];
    }

    private function mapUpdateData(Service $service, array $data): array
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

        if (array_key_exists('link_url', $data)) {
            $payload['link_url'] = $data['link_url'];
        }

        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (bool) $data['is_active'];
        }

        if (array_key_exists('is_popular', $data)) {
            $payload['is_popular'] = (bool) $data['is_popular'];
        }

        if (array_key_exists('sort_order', $data)) {
            $payload['sort_order'] = (int) $data['sort_order'];
        }

        if (array_key_exists('metadata', $data)) {
            $payload['metadata'] = Arr::get($data, 'metadata', $service->metadata ?? []);
        }

        return $payload;
    }

    private function handleUploads(Service $service, array $data): void
    {
        if ($image = Arr::get($data, 'image')) {
            // Eliminar archivo antiguo (verificar en ambos discos por compatibilidad)
            $this->deleteFile($service->image_path, 'public');
            $this->deleteFile($service->image_path, 'public_storage');

            // Asegurar que el directorio existe
            $imageDir = 'services/images';
            $publicStorageDisk = Storage::disk('public_storage');
            if (! $publicStorageDisk->exists($imageDir)) {
                $publicStorageDisk->makeDirectory($imageDir, 0755, true);
            }

            // Usar 'public_storage' para guardar directamente en public/storage/
            $service->image_path = $this->storeFile($image, $imageDir, 'public_storage');
        }
    }

    private function storeFile(UploadedFile $file, string $directory, string $disk): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($directory, $filename, ['disk' => $disk]);
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
        }
    }
}
