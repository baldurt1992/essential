<?php

namespace App\Http\Controllers\Site;

use App\Domain\Support\DataTransferObjects\ContactMessageData;
use App\Domain\Support\Services\ContactMessageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ContactMessageRequest;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    public function __construct(
        private readonly ContactMessageService $contactMessageService,
    ) {
    }

    public function store(ContactMessageRequest $request): JsonResponse
    {
        $contactMessage = ContactMessageData::fromArray($request->validated());

        $this->contactMessageService->handle($contactMessage);

        return response()->json([
            'message' => 'Gracias por escribirnos. Te contactaremos muy pronto.',
        ], 201);
    }
}


