<?php

namespace App\Http\Middleware;

use App\Facades\Features;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureSlug): Response
    {
        if (!Features::hasFeature($featureSlug)) {
            // Log attempt
            Log::warning("Access denied: Tenant " . (auth()->user()?->tenant_id ?? 'guest') . " attempted to access feature: {$featureSlug}");

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This feature is not available in your current plan. Please upgrade.',
                    'upgrade_info' => Features::getUpgradeInfo($featureSlug)
                ], 403);
            }

            abort(403, 'This feature is not available in your current plan. Please upgrade.');
        }

        return $next($request);
    }
}
