<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\Models\Template;
use App\Domain\Catalog\Services\TemplateAccessService;
use App\Http\Resources\PublicTemplateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateCatalogController extends Controller
{
    public function __construct(private readonly TemplateAccessService $accessService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 12), 50);

        $templates = Template::query()
            ->active()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $user = $request->user();

        $templates->getCollection()->transform(function (Template $template) use ($user) {
            $template->is_accessible = $this->accessService->userHasAccess($user, $template);

            return $template;
        });

        return PublicTemplateResource::collection($templates)->response();
    }

    public function show(Request $request, string $identifier): JsonResponse
    {
        $template = Template::query()
            ->where('slug', $identifier)
            ->orWhere('uuid', $identifier)
            ->firstOrFail();

        $template->is_accessible = $this->accessService->userHasAccess($request->user(), $template);

        return (new PublicTemplateResource($template))->response();
    }
}
