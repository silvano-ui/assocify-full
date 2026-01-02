<?php

namespace Modules\Reports\Services;

use App\Core\Tenant\Tenant;

class FiscalReportService
{
    public function generateRegistroIVA(Tenant $tenant, int $year, int $month): void
    {
        // Logic to generate IVA register
    }

    public function generatePrimaNota(Tenant $tenant, int $year, int $month): void
    {
        // Logic to generate Prima Nota
    }

    public function generateRiepilogoQuote(Tenant $tenant, int $year): void
    {
        // Logic to generate Summary of Fees
    }
}
