<?php

namespace Modules\WhiteLabel\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\WhiteLabel\Services\PwaService;

class ManifestController extends Controller
{
    protected PwaService $pwaService;

    public function __construct(PwaService $pwaService)
    {
        $this->pwaService = $pwaService;
    }

    public function manifest()
    {
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id');
        
        // If accessed publicly (e.g. from browser request), we might need to resolve tenant from domain again
        // But Middleware `ResolveTenantByDomain` should handle this if applied to this route.
        // Assuming Middleware is applied.
        
        if (!$tenantId) {
            // Try to resolve from domain if middleware didn't run or session is empty (stateless request)
             // This logic is better placed in Middleware, but for safety:
             // We can return default manifest.
             return response()->json(['name' => config('app.name')]);
        }

        $manifest = $this->pwaService->getManifest($tenantId);
        return response()->json($manifest);
    }

    public function serviceWorker()
    {
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id');
        $config = $this->pwaService->getServiceWorkerConfig($tenantId ?? 0);
        
        // Return JS file with dynamic config
        $js = "
            const CACHE_NAME = 'assocify-cache-v1';
            const urlsToCache = [
                '/',
                '/offline',
                '/css/app.css',
                '/js/app.js'
            ];

            self.addEventListener('install', event => {
                event.waitUntil(
                    caches.open(CACHE_NAME)
                        .then(cache => {
                            return cache.addAll(urlsToCache);
                        })
                );
            });

            self.addEventListener('fetch', event => {
                event.respondWith(
                    caches.match(event.request)
                        .then(response => {
                            if (response) {
                                return response;
                            }
                            return fetch(event.request);
                        })
                );
            });
        ";

        return response($js)->header('Content-Type', 'application/javascript');
    }

    public function offline()
    {
        return view('whitelabel::pwa.offline');
    }
}
