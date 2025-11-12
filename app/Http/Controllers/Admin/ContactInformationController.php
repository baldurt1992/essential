<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Settings\Services\ContactInformationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactInformationRequest;
use App\Http\Resources\Admin\ContactInformationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactInformationController extends Controller
{
    public function __construct(private readonly ContactInformationService $service) {}

    public function show(Request $request): JsonResponse
    {
        $contact = $this->service->get();

        return (new ContactInformationResource($contact))->response();
    }

    public function update(UpdateContactInformationRequest $request): JsonResponse
    {
        $contact = $this->service->update($request->user(), $request->validated());

        return (new ContactInformationResource($contact))->response();
    }
}

