<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\Template;
use App\Domain\Catalog\Services\TemplateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTemplateRequest;
use App\Http\Requests\Admin\UpdateTemplateRequest;
use App\Http\Resources\Admin\TemplateResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TemplateController extends Controller
{
    public function __construct(private readonly TemplateService $templateService) {}

    public function index(): JsonResponse
    {
        $templates = Template::query()
            ->latest()
            ->paginate(15);

        return TemplateResource::collection($templates)->response();
    }

    public function store(StoreTemplateRequest $request): JsonResponse
    {
        Log::debug('[admin][templates][store] payload', $request->except(['preview_image', 'package_file']));
        Log::debug('[admin][templates][store] has files', [
            'preview_image' => $request->hasFile('preview_image'),
            'package_file' => $request->hasFile('package_file'),
        ]);

        $template = $this->templateService->store($request->user(), $request->validated());

        return (new TemplateResource($template))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Template $template): JsonResponse
    {
        return (new TemplateResource($template))->response();
    }

    public function update(UpdateTemplateRequest $request, Template $template): JsonResponse
    {
        // Verificar límites de PHP antes de procesar
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');
        $maxExecutionTime = ini_get('max_execution_time');
        $memoryLimit = ini_get('memory_limit');

        // Verificar tamaño del contenido POST
        $contentLength = $request->header('Content-Length');
        $postDataSize = strlen($request->getContent());

        Log::info('[admin][templates][update] Starting update', [
            'template_id' => $template->id,
            'has_preview_image' => $request->hasFile('preview_image'),
            'has_package_file' => $request->hasFile('package_file'),
            'current_download_path' => $template->download_path,
            'all_request_keys' => array_keys($request->all()),
            'content_length' => $contentLength,
            'post_data_size' => $postDataSize,
            'post_data_size_mb' => round($postDataSize / (1024 * 1024), 2),
            'php_limits' => [
                'upload_max_filesize' => $uploadMaxFilesize,
                'post_max_size' => $postMaxSize,
                'max_execution_time' => $maxExecutionTime,
                'memory_limit' => $memoryLimit,
            ],
            'all_files' => array_keys($request->allFiles()),
            'request_method' => $request->method(),
            'is_multipart' => str_contains($request->header('Content-Type', ''), 'multipart/form-data'),
        ]);

        $data = $request->validated();

        Log::info('[admin][templates][update] Validated data keys', [
            'validated_keys' => array_keys($data),
            'has_package_file_in_data' => isset($data['package_file']),
        ]);

        // Incluir archivos en los datos si están presentes
        if ($request->hasFile('preview_image')) {
            $previewFile = $request->file('preview_image');
            Log::info('[admin][templates][update] Preview image file details', [
                'original_name' => $previewFile->getClientOriginalName(),
                'mime_type' => $previewFile->getMimeType(),
                'size' => $previewFile->getSize(),
                'is_valid' => $previewFile->isValid(),
            ]);
            $data['preview_image'] = $previewFile;
        }

        if ($request->hasFile('package_file')) {
            $packageFile = $request->file('package_file');

            // Verificar que el archivo sea válido y tenga contenido
            if (!$packageFile->isValid()) {
                Log::error('[admin][templates][update] Package file is invalid', [
                    'error' => $packageFile->getError(),
                    'error_message' => $packageFile->getErrorMessage(),
                ]);
                return response()->json([
                    'message' => 'El archivo no es válido. Error: ' . $packageFile->getErrorMessage(),
                ], 422);
            }

            Log::info('[admin][templates][update] Package file details', [
                'original_name' => $packageFile->getClientOriginalName(),
                'mime_type' => $packageFile->getMimeType(),
                'size' => $packageFile->getSize(),
                'size_mb' => round($packageFile->getSize() / (1024 * 1024), 2),
                'is_valid' => $packageFile->isValid(),
                'current_template_download_path' => $template->download_path,
            ]);
            $data['package_file'] = $packageFile;
        } else {
            Log::warning('[admin][templates][update] No package file in request', [
                'hasFile_check' => $request->hasFile('package_file'),
                'all_files' => $request->allFiles(),
            ]);
        }

        $updated = $this->templateService->update($template, $data);

        Log::info('[admin][templates][update] Update completed', [
            'template_id' => $updated->id,
            'preview_image_path' => $updated->preview_image_path,
            'download_path' => $updated->download_path,
            'download_path_changed' => $updated->download_path !== $template->download_path,
        ]);

        return (new TemplateResource($updated))->response();
    }

    public function destroy(Template $template): JsonResponse
    {
        $this->templateService->delete($template);

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }

    public function deletePackageFile(Template $template): JsonResponse
    {
        Log::info('[admin][templates][delete-package] Starting', [
            'template_id' => $template->id,
            'current_download_path' => $template->download_path,
        ]);

        $this->templateService->deletePackageFile($template);

        Log::info('[admin][templates][delete-package] Completed', [
            'template_id' => $template->id,
        ]);

        return (new TemplateResource($template->refresh()))->response();
    }
}
