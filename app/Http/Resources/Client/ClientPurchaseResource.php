<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\PublicTemplateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Billing\Models\Purchase */
class ClientPurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'purchase_code' => $this->purchase_code,
            'amount' => $this->amount,
            'amount_cents' => $this->amount_cents,
            'currency' => $this->currency,
            'status' => $this->status,
            'purchased_at' => $this->purchased_at,
            'template' => new PublicTemplateResource($this->whenLoaded('template')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

