<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\Service;
use App\Domain\Catalog\Services\ServiceService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    public function __construct(private readonly ServiceService $serviceService) {}

    public function index(): JsonResponse
    {
        $services = Service::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(15);

        return ServiceResource::collection($services)->response();
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Agregar el archivo de imagen si está presente
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        
        $service = $this->serviceService->store($request->user(), $data);

        return (new ServiceResource($service))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Service $service): JsonResponse
    {
        return (new ServiceResource($service))->response();
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $data = $request->validated();
        
        // Agregar el archivo de imagen si está presente
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        
        $updated = $this->serviceService->update($service, $data);

        return (new ServiceResource($updated))->response();
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->serviceService->delete($service);

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
