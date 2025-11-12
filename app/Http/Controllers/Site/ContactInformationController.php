<?php

namespace App\Http\Controllers\Site;

use App\Domain\Settings\Services\ContactInformationService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactInformationResource;
use Illuminate\Http\JsonResponse;

class ContactInformationController extends Controller
{
    public function __construct(private readonly ContactInformationService $service) {}

    public function show(): JsonResponse
    {
        $contact = $this->service->get();

        return (new ContactInformationResource($contact))->response();
    }
}
