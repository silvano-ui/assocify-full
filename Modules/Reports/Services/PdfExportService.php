<?php

namespace Modules\Reports\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Reports\Entities\ReportTemplate;

class PdfExportService
{
    public function generate(string $path, ReportTemplate $template, array $columns, array $data): void
    {
        $pdf = Pdf::loadView('reports::pdf.template', [
            'template' => $template,
            'columns' => $columns,
            'data' => $data,
            'generated_at' => now(),
        ]);

        $pdf->save($path);
    }
}
