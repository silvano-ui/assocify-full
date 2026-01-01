<?php

namespace Modules\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Api\Entities\ApiKey;
use Illuminate\Support\Facades\Cache;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-KEY');

        if (!$key) {
            return response()->json(['message' => 'API Key missing'], 401);
        }

        // Cache key lookup for performance
        $apiKey = Cache::remember('api_key_' . $key, 60, function () use ($key) {
            return ApiKey::where('key', $key)->first();
        });

        if (!$apiKey || !$apiKey->is_active) {
            return response()->json(['message' => 'Invalid or inactive API Key'], 401);
        }

        // Check if key is expired
        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return response()->json(['message' => 'API Key expired'], 401);
        }

        // Update last used at (fire and forget or async job preferred, but simple update here)
        $apiKey->update(['last_used_at' => now()]);

        // Set tenant context (assuming Stancl/Tenancy or similar, or just global scope)
        // For now, we'll store the tenant_id in the request attributes
        $request->attributes->set('tenant_id', $apiKey->tenant_id);
        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }
}
