<?php

namespace Modules\Api\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $tenantId = $this->attributes->get('tenant_id');
        if (!$tenantId && $this->user()) {
            $tenantId = $this->user()->tenant_id;
        }

        return [
            'event_category_id' => 'nullable|exists:event_categories,id',
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('events')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'registration_starts' => 'nullable|date',
            'registration_ends' => 'nullable|date|after:registration_starts',
            'max_participants' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'requires_approval' => 'boolean',
            'is_public' => 'boolean',
            'status' => 'required|in:draft,published,cancelled,completed',
            'cover_image' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
        ];
    }
}
