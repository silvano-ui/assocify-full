<?php

namespace Modules\Api\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // Avoid exposing sensitive data like created_at, updated_at unless necessary
        ];
    }
}
