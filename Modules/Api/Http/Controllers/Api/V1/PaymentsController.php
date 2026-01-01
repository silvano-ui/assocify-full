<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Modules\Payments\Entities\Transaction;
use Modules\Payments\Entities\Invoice;
use Modules\Api\Http\Requests\V1\StoreTransactionRequest;
use Modules\Api\Http\Resources\V1\TransactionResource;

class PaymentsController extends BaseApiController
{
    public function index()
    {
        return $this->paginate(Transaction::with('user', 'paymentMethod'), 15, TransactionResource::class);
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = Transaction::create($request->validated());
        return $this->success(new TransactionResource($transaction), 'Payment recorded successfully', 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['user', 'paymentMethod', 'invoice'])->findOrFail($id);
        return $this->success(new TransactionResource($transaction));
    }

    public function invoices()
    {
        return $this->paginate(Invoice::query());
    }

    public function invoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        return $this->success($invoice);
    }

    public function invoicePdf($id)
    {
        $invoice = Invoice::findOrFail($id);
        // Placeholder for PDF generation
        return $this->success([
            'url' => url("/api/v1/invoices/{$id}/download"), // Hypothetical download URL
            'message' => 'PDF generation to be implemented'
        ]);
    }
}
