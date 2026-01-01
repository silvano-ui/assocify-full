<?php

namespace Modules\Payments\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Entities\Invoice;
use Modules\Payments\Entities\Transaction;
use App\Core\Users\User;
use App\Core\Tenant\Tenant;

class PaymentsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        
        // Ensure user exists (assuming DatabaseSeeder ran first)
        $user = User::where('tenant_id', $tenantId)->first();
        if (!$user) {
             return;
        }

        // Create Payment Methods
        PaymentMethod::create([
            'tenant_id' => $tenantId,
            'type' => 'bank_transfer',
            'name' => 'Bonifico Bancario',
            'is_active' => true,
            'is_default' => true,
            'credentials' => ['iban' => 'IT000000000000000000000000000'],
        ]);

        PaymentMethod::create([
            'tenant_id' => $tenantId,
            'type' => 'cash',
            'name' => 'Contanti',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Create Invoices
        $invoices = [];
        for ($i = 1; $i <= 10; $i++) {
            $amount = rand(50, 200);
            $invoices[] = Invoice::create([
                'tenant_id' => $tenantId,
                'user_id' => $user->id,
                'number' => 'INV-2026-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'type' => 'membership',
                'items' => [
                    ['description' => 'Annual Membership', 'amount' => $amount]
                ],
                'subtotal' => $amount,
                'tax_rate' => 0,
                'tax' => 0,
                'total' => $amount,
                'status' => $i > 5 ? 'paid' : 'pending',
                'due_date' => now()->addDays(30),
                'paid_at' => $i > 5 ? now() : null,
            ]);
        }

        // Create Transactions for paid invoices
        foreach ($invoices as $invoice) {
            if ($invoice->status === 'paid') {
                Transaction::create([
                    'tenant_id' => $tenantId,
                    'invoice_id' => $invoice->id,
                    'user_id' => $user->id,
                    'type' => 'payment',
                    'amount' => $invoice->total,
                    'status' => 'completed',
                    'processed_at' => now(),
                    'reference' => 'TX-' . uniqid(),
                ]);
            }
        }
    }
}
