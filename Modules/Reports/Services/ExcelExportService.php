<?php

namespace Modules\Reports\Services;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Reports\Exports\ReportExport;

class ExcelExportService
{
    public function generate(string $path, array $columns, array $data, string $format): void
    {
        // Using a generic Export class for Maatwebsite/Excel
        // Assuming ReportExport exists or creating it dynamically
        
        Excel::store(new class($columns, $data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $columns;
            protected $data;

            public function __construct($columns, $data) {
                $this->columns = $columns;
                $this->data = collect($data);
            }

            public function collection() {
                return $this->data;
            }

            public function headings(): array {
                return $this->columns;
            }
        }, $path);
    }
}
