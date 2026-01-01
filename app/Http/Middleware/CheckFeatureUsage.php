<?php

namespace App\Http\Middleware;

use App\Facades\Features;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureUsage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureSlug, int $quantity = 1): Response
    {
        $check = Features::canUse($featureSlug, $quantity);

        if (!$check['allowed']) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $check['message']], 403);
            }
            abort(403, $check['message']);
        }

        // Increment usage
        // Note: Middleware usually runs BEFORE the controller action.
        // If we increment here, and the action fails, we might have over-counted.
        // However, checking usage is done here. Incrementing might be better done in the controller or "terminate" middleware.
        // BUT the requirement says: "Verifica e incrementa usage". So I will increment here.
        
        Features::incrementUsage($featureSlug, $quantity);

        if ($check['soft_warning']) {
            // How to pass warning to view/response? 
            // Flash session or header.
            if ($request->hasSession()) {
                session()->flash('warning', $check['message']);
            }
            // Or header
            // $response->headers->set('X-Feature-Warning', $check['message']);
            // But we can't modify response here easily if we return $next($request).
            // We can check response after $next.
        }

        $response = $next($request);

        if ($check['soft_warning'] && method_exists($response, 'header')) {
            $response->header('X-Feature-Warning', $check['message']);
        }

        return $response;
    }
}
