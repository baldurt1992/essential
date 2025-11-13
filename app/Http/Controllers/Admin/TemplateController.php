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
        \Log::debug('[admin][templates][store] payload', $request->except(['preview_image', 'package_file']));
        \Log::debug('[admin][templates][store] has files', [
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
        \Log::info('[admin][templates][update] Starting update', [
            'template_id' => $template->id,
            'has_preview_image' => $request->hasFile('preview_image'),
            'has_package_file' => $request->hasFile('package_file'),
        ]);

        $data = $request->validated();
        
        // Incluir archivos en los datos si están presentes
        if ($request->hasFile('preview_image')) {
            $previewFile = $request->file('preview_image');
            \Log::info('[admin][templates][update] Preview image file details', [
                'original_name' => $previewFile->getClientOriginalName(),
                'mime_type' => $previewFile->getMimeType(),
                'size' => $previewFile->getSize(),
                'is_valid' => $previewFile->isValid(),
            ]);
            $data['preview_image'] = $previewFile;
        }
        
        if ($request->hasFile('package_file')) {
            $packageFile = $request->file('package_file');
            \Log::info('[admin][templates][update] Package file details', [
                'original_name' => $packageFile->getClientOriginalName(),
                'mime_type' => $packageFile->getMimeType(),
                'size' => $packageFile->getSize(),
                'is_valid' => $packageFile->isValid(),
            ]);
            $data['package_file'] = $packageFile;
        }

        $updated = $this->templateService->update($template, $data);

        \Log::info('[admin][templates][update] Update completed', [
            'template_id' => $updated->id,
            'preview_image_path' => $updated->preview_image_path,
            'download_path' => $updated->download_path,
        ]);

        return (new TemplateResource($updated))->response();
    }

    public function destroy(Template $template): JsonResponse
    {
        $this->templateService->delete($template);

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
