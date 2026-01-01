<?php

use Illuminate\Support\Facades\Route;
use Modules\WhiteLabel\Http\Controllers\DomainController;
use Modules\WhiteLabel\Http\Controllers\BrandingController;
use Modules\WhiteLabel\Http\Controllers\ManifestController;
use Modules\WhiteLabel\Http\Middleware\ResolveTenantByDomain;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes (PWA)
Route::middleware([ResolveTenantByDomain::class])->group(function() {
    Route::get('/manifest.json', [ManifestController::class, 'manifest'])->name('whitelabel.manifest');
    Route::get('/service-worker.js', [ManifestController::class, 'serviceWorker'])->name('whitelabel.sw');
    Route::get('/offline', [ManifestController::class, 'offline'])->name('whitelabel.offline');
});

// Authenticated routes (Management)
Route::middleware(['auth', 'verified'])->prefix('whitelabel')->group(function() {
    // Branding
    Route::get('branding/preview', [BrandingController::class, 'preview'])->name('whitelabel.branding.preview');
    Route::post('branding/update', [BrandingController::class, 'update'])->name('whitelabel.branding.update');
    Route::post('branding/reset', [BrandingController::class, 'reset'])->name('whitelabel.branding.reset');
    Route::post('branding/upload-asset', [BrandingController::class, 'uploadAsset'])->name('whitelabel.branding.upload-asset');
    
    // Domain
    Route::post('domain/check-availability', [DomainController::class, 'checkAvailability'])->name('whitelabel.domain.check');
    Route::post('domain/register', [DomainController::class, 'register'])->name('whitelabel.domain.register');
    Route::post('domain/transfer', [DomainController::class, 'transfer'])->name('whitelabel.domain.transfer');
    Route::post('domain/nameservers', [DomainController::class, 'updateNameservers'])->name('whitelabel.domain.nameservers');
    Route::get('domain/dns', [DomainController::class, 'getDnsRecords'])->name('whitelabel.domain.dns');
    Route::post('domain/dns', [DomainController::class, 'updateDnsRecord'])->name('whitelabel.domain.dns.update');
});
