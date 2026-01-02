<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Reports\Services\ShareService;
use Illuminate\Support\Facades\Storage;

class ShareController extends Controller
{
    protected $shareService;

    public function __construct(ShareService $shareService)
    {
        $this->shareService = $shareService;
    }

    public function view($token)
    {
        if (!$this->shareService->validateToken($token)) {
            abort(404, 'Link expired or invalid');
        }

        $share = \Modules\Reports\Entities\ReportShare::where('token', $token)->firstOrFail();
        
        // Record access
        $share->recordAccess(request());

        return view('reports::public.share', ['share' => $share]);
    }

    public function download($token, Request $request)
    {
        if (!$this->shareService->validateToken($token)) {
            abort(403, 'Link expired or invalid');
        }

        $share = \Modules\Reports\Entities\ReportShare::where('token', $token)->firstOrFail();
        
        if ($share->password && !$share->checkPassword($request->password)) {
            abort(403, 'Invalid password');
        }

        $filePath = $share->generatedReport->file_path;

        if (!Storage::disk('reports')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('reports')->download($filePath);
    }
}
