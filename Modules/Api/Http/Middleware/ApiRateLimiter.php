<?php

namespace Modules\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiRateLimiter
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->attributes->get('api_key');

        // If no API key (e.g. public route or skipped validation), skip rate limiting
        if (!$apiKey) {
            return $next($request);
        }

        // Simple Rate Limiting (per minute)
        $keyLimit = 'api_limit_' . $apiKey->id;
        if (Cache::has($keyLimit) && Cache::get($keyLimit) >= $apiKey->rate_limit_per_minute) {
             return response()->json(['message' => 'Rate limit exceeded'], 429);
        }
        Cache::increment($keyLimit);
        if (!Cache::has($keyLimit)) {
            Cache::put($keyLimit, 1, 60);
        }

        return $next($request);
    }
}
