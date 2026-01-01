<?php

namespace Modules\Api\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', function() {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'location' => [
                'name' => $this->location,
                'address' => $this->address,
                'coordinates' => [
                    'lat' => $this->lat,
                    'lng' => $this->lng,
                ],
            ],
            'schedule' => [
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
            ],
            'registration' => [
                'starts_at' => $this->registration_starts,
                'ends_at' => $this->registration_ends,
                'max_participants' => $this->max_participants,
                'requires_approval' => $this->requires_approval,
            ],
            'pricing' => [
                'price' => $this->price,
                'is_free' => $this->is_free,
                'currency' => 'EUR', // Assuming default or fetch from tenant settings
            ],
            'status' => $this->status,
            'is_public' => $this->is_public,
            'cover_image' => $this->cover_image, // Ideally full URL
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
