<?php

namespace Modules\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Api\Entities\ApiKey;
use Modules\Api\Entities\ApiRequestLog;
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

        // Simple Rate Limiting (per minute)
        $keyLimit = 'api_limit_' . $apiKey->id;
        if (Cache::has($keyLimit) && Cache::get($keyLimit) >= $apiKey->rate_limit_per_minute) {
             return response()->json(['message' => 'Rate limit exceeded'], 429);
        }
        Cache::increment($keyLimit);
        if (!Cache::has($keyLimit)) {
            Cache::put($keyLimit, 1, 60);
        }

        // Log request (Async preferred, but direct for now)
        $logEntry = ApiRequestLog::create([
            'tenant_id' => $apiKey->tenant_id,
            'api_key_id' => $apiKey->id,
            'method' => $request->method(),
            'endpoint' => $request->path(),
            'response_status' => 0, // Will be updated after response
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_headers' => json_encode($request->headers->all()),
            'request_body' => json_encode($request->all()),
            'response_time_ms' => 0,
        ]);

        $start = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $start) * 1000);

        // Update log with response info
        // Note: This is simplified. In production, use terminable middleware or events.
        if ($logEntry) {
            $logEntry->update([
                'response_status' => $response->getStatusCode(),
                // 'response_headers' => json_encode($response->headers->all()), // Not in migration 2026_01_01_000000 but maybe added later?
                // 'response_body' => $response->getContent(), // Not in migration 2026_01_01_000000?
                'response_time_ms' => $duration,
            ]);
        }

        return $response;
    }
}
