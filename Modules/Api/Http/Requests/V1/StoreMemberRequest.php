<?php

namespace Modules\Api\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
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
            'user_id' => 'nullable|exists:users,id',
            'member_number' => [
                'required',
                'string',
                Rule::unique('member_profiles')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'first_name' => 'required_without:user_id|string|max:255', // Assuming we might handle profile w/o user or user creation later
            'last_name' => 'required_without:user_id|string|max:255',
            'email' => 'nullable|email|max:255',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'fiscal_code' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('member_profiles')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:2',
            'document_type' => 'nullable|in:id_card,passport,driving_license,other',
            'document_number' => 'nullable|string|max:255',
            'document_expires' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'custom_fields' => 'nullable|array',
        ];
    }
}
