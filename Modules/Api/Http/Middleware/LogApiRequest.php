<?php

namespace Modules\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Api\Entities\ApiRequestLog;

class LogApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->attributes->get('api_key');
        $tenantId = $request->attributes->get('tenant_id');

        $logEntry = ApiRequestLog::create([
            'tenant_id' => $tenantId,
            'api_key_id' => $apiKey ? $apiKey->id : null,
            'method' => $request->method(),
            'endpoint' => $request->path(),
            'response_status' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_headers' => json_encode($request->headers->all()),
            'request_body' => json_encode($request->all()),
            'response_time_ms' => 0,
        ]);

        $start = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $start) * 1000);

        if ($logEntry) {
            $logEntry->update([
                'response_status' => $response->getStatusCode(),
                'response_time_ms' => $duration,
            ]);
        }

        return $response;
    }
}
