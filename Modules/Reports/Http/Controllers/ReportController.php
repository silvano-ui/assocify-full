<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Reports\Entities\GeneratedReport;
use Modules\Reports\Entities\ReportTemplate;
use Modules\Reports\Services\ExportService;
use Modules\Reports\Services\DataSourceService;
use Modules\Reports\Services\ShareService;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $exportService;
    protected $dataSourceService;
    protected $shareService;

    public function __construct(
        ExportService $exportService,
        DataSourceService $dataSourceService,
        ShareService $shareService
    ) {
        $this->exportService = $exportService;
        $this->dataSourceService = $dataSourceService;
        $this->shareService = $shareService;
    }

    public function download(GeneratedReport $generatedReport)
    {
        // Check authorization (e.g., tenant check via global scope or policy)
        // Global scope handles tenant check if user is logged in
        
        if (!Storage::disk('reports')->exists($generatedReport->file_path)) {
            abort(404);
        }

        return Storage::disk('reports')->download($generatedReport->file_path);
    }

    public function quickExport(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:report_templates,id',
            'format' => 'required|in:pdf,xlsx,csv',
        ]);

        $template = ReportTemplate::findOrFail($request->template_id);
        $generatedReport = $this->exportService->generate($template, $request->format);

        return response()->json([
            'message' => 'Export started',
            'report_id' => $generatedReport->id
        ]);
    }

    // API Methods

    public function types()
    {
        return response()->json(config('reports.data_sources'));
    }

    public function columns($dataSource)
    {
        return response()->json($this->dataSourceService->getAvailableColumns($dataSource));
    }

    public function generate(Request $request)
    {
        // Similar to quickExport but API focused
        return $this->quickExport($request);
    }

    public function status(GeneratedReport $report)
    {
        return response()->json([
            'status' => $report->status,
            'download_url' => $report->isCompleted() ? route('reports.download', $report) : null
        ]);
    }

    public function share(Request $request, GeneratedReport $report)
    {
        $request->validate([
            'expires_at' => 'nullable|date',
            'password' => 'nullable|string'
        ]);

        $share = $this->shareService->createShare($report, $request->all());

        return response()->json([
            'share_url' => $share->getPublicUrl()
        ]);
    }
}
