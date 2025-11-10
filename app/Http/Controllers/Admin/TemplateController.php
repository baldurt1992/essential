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
        $updated = $this->templateService->update($template, $request->validated());

        return (new TemplateResource($updated))->response();
    }

    public function destroy(Template $template): JsonResponse
    {
        $this->templateService->delete($template);

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
