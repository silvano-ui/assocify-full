<?php

namespace Modules\WhiteLabel\Services;

use Modules\WhiteLabel\Entities\TenantPdfBranding;
use Illuminate\Support\Facades\Storage;

class PdfBrandingService
{
    public function getHeaderHtml(int $tenantId): string
    {
        $branding = TenantPdfBranding::where('tenant_id', $tenantId)->first();
        return $branding ? ($branding->header_text ?? '') : '';
    }

    public function getFooterHtml(int $tenantId): string
    {
        $branding = TenantPdfBranding::where('tenant_id', $tenantId)->first();
        return $branding ? ($branding->footer_text ?? '') : '';
    }

    public function getWatermark(int $tenantId): ?array
    {
        $branding = TenantPdfBranding::where('tenant_id', $tenantId)->first();
        if (!$branding) return null;

        if ($branding->watermark_image_path) {
            return [
                'type' => 'image',
                'path' => Storage::path($branding->watermark_image_path),
                'opacity' => $branding->watermark_opacity,
            ];
        }

        if ($branding->watermark_text) {
            return [
                'type' => 'text',
                'text' => $branding->watermark_text,
                'opacity' => $branding->watermark_opacity,
            ];
        }

        return null;
    }

    public function getPaperConfig(int $tenantId): array
    {
        $branding = TenantPdfBranding::where('tenant_id', $tenantId)->first();
        return [
            'size' => $branding->paper_size ?? 'A4',
            'margins' => $branding->margins ?? ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10],
        ];
    }

    public function applyBrandingToPdf(int $tenantId, $pdf): void
    {
        // Implementation depends on PDF library (e.g. DomPDF, Snappy)
        // This is a placeholder for the logic.
    }
}
