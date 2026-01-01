<?php

namespace Modules\Api\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'reference' => $this->reference,
            'external_id' => $this->external_id,
            'processed_at' => $this->processed_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'invoice_id' => $this->invoice_id, // Or relation if needed
            'payment_method' => $this->whenLoaded('paymentMethod', function() {
                 return [
                     'id' => $this->paymentMethod->id,
                     'name' => $this->paymentMethod->name,
                 ];
            }),
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
