<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Services\PlanService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlanRequest;
use App\Http\Requests\Admin\UpdatePlanRequest;
use App\Http\Resources\Admin\PlanResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PlanController extends Controller
{
    public function __construct(private readonly PlanService $planService)
    {
    }

    public function index(): JsonResponse
    {
        $plans = Plan::query()
            ->latest()
            ->paginate(15);

        return PlanResource::collection($plans)->response();
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = $this->planService->store($request->user(), $request->validated());

        return (new PlanResource($plan))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Plan $plan): JsonResponse
    {
        return (new PlanResource($plan))->response();
    }

    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        $updated = $this->planService->update($plan, $request->validated());

        return (new PlanResource($updated))->response();
    }

    public function destroy(Plan $plan): JsonResponse
    {
        $this->planService->delete($plan);

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
