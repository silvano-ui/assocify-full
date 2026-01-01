<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Payments\Entities\Transaction;
use Modules\Payments\Entities\Invoice;

class PaymentsController extends BaseApiController
{
    public function index()
    {
        return $this->paginate(Transaction::query());
    }

    public function store(Request $request)
    {
        $transaction = Transaction::create($request->all());
        return $this->success($transaction, 'Payment recorded successfully', 201);
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return $this->success($transaction);
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
