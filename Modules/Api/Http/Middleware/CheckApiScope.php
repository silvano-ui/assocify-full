<?php

namespace Modules\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiScope
{
    public function handle(Request $request, Closure $next, $scope)
    {
        $apiKey = $request->attributes->get('api_key');

        if (!$apiKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if key has the required scope (permissions column in ApiKey is array/json)
        // Assuming permissions is a JSON array like ['read', 'write', 'delete']
        // and $scope is passed like 'read' or 'write'
        
        $permissions = $apiKey->permissions ?? [];
        
        // Admin keys or wildcard permissions
        if (in_array('*', $permissions)) {
            return $next($request);
        }

        if (!in_array($scope, $permissions)) {
            return response()->json(['message' => 'Insufficient permissions'], 403);
        }

        return $next($request);
    }
}
