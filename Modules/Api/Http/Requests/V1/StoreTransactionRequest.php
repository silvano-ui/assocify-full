<?php

namespace Modules\Api\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_id' => 'nullable|exists:invoices,id',
            'user_id' => 'nullable|exists:users,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'type' => 'required|string|max:50', // e.g., payment, refund
            'amount' => 'required|numeric',
            'currency' => 'required|string|size:3',
            'reference' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255',
            'status' => 'required|string|max:50', // e.g., pending, completed, failed
            'metadata' => 'nullable|array',
            'processed_at' => 'nullable|date',
        ];
    }
}
