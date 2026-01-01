<?php

namespace Modules\WhiteLabel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\WhiteLabel\Entities\TenantBranding;
use Modules\WhiteLabel\Services\BrandingService;

class BrandingController extends Controller
{
    protected BrandingService $brandingService;

    public function __construct(BrandingService $brandingService)
    {
        $this->brandingService = $brandingService;
    }

    public function update(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        if (!$tenantId) {
            return response()->json(['message' => 'No tenant context'], 403);
        }

        $data = $request->validate([
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'font_family' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'theme_mode' => 'nullable|in:light,dark,system',
        ]);

        $branding = TenantBranding::firstOrCreate(['tenant_id' => $tenantId]);
        $branding->update($data);

        return response()->json(['message' => 'Branding updated successfully', 'branding' => $branding]);
    }

    public function preview(Request $request)
    {
        // Return a view that loads the branding settings for preview
        $tenantId = auth()->user()->tenant_id;
        $branding = $this->brandingService->getBranding($tenantId);
        
        return view('whitelabel::branding.preview', compact('branding'));
    }

    public function reset(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        if (!$tenantId) {
            return response()->json(['message' => 'No tenant context'], 403);
        }

        $branding = TenantBranding::where('tenant_id', $tenantId)->first();
        if ($branding) {
            // Reset specific fields or delete to fallback to defaults?
            // Usually reset means clearing overrides.
            $branding->update([
                'primary_color' => null,
                'secondary_color' => null,
                'accent_color' => null,
                'custom_css' => null,
                'custom_js' => null,
                'logo_path' => null,
                'favicon_path' => null,
            ]);
        }

        return response()->json(['message' => 'Branding reset to defaults']);
    }

    public function uploadAsset(Request $request)
    {
        $request->validate([
            'type' => 'required|in:logo,logo_dark,favicon,icon',
            'file' => 'required|image|max:2048', // 2MB max
        ]);

        $tenantId = auth()->user()->tenant_id;
        if (!$tenantId) {
            return response()->json(['message' => 'No tenant context'], 403);
        }

        $file = $request->file('file');
        $path = $file->store("tenants/{$tenantId}/branding", 'public');

        $branding = TenantBranding::firstOrCreate(['tenant_id' => $tenantId]);
        
        switch ($request->type) {
            case 'logo':
                $branding->update(['logo_path' => $path]);
                break;
            case 'logo_dark':
                $branding->update(['logo_dark_path' => $path]);
                break;
            case 'favicon':
                $branding->update(['favicon_path' => $path]);
                break;
        }

        return response()->json(['message' => 'Asset uploaded', 'path' => Storage::url($path)]);
    }
}
