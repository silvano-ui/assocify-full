<?php

namespace Modules\Api\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'member_number' => $this->member_number,
            'first_name' => $this->first_name, // Accessor or from User
            'last_name' => $this->last_name,   // Accessor or from User
            'user' => new UserResource($this->whenLoaded('user')),
            'birth_date' => $this->birth_date,
            'birth_place' => $this->birth_place,
            'fiscal_code' => $this->fiscal_code,
            'address_info' => [
                'address' => $this->address,
                'city' => $this->city,
                'province' => $this->province,
                'zip' => $this->zip,
                'country' => $this->country,
            ],
            'document_info' => [
                'type' => $this->document_type,
                'number' => $this->document_number,
                'expires_at' => $this->document_expires,
            ],
            'contacts' => [
                'emergency_contact' => $this->emergency_contact,
                'emergency_phone' => $this->emergency_phone,
            ],
            'notes' => $this->notes,
            'custom_fields' => $this->custom_fields,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
