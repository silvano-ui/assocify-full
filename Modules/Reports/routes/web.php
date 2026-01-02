<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportController;
use Modules\Reports\Http\Controllers\ShareController;

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

Route::prefix('reports')->middleware(['auth'])->name('reports.')->group(function () {
    Route::get('/download/{generatedReport}', [ReportController::class, 'download'])->name('download');
    Route::post('/quick-export', [ReportController::class, 'quickExport'])->name('quick-export');
});

Route::prefix('r')->name('reports.share.')->group(function () {
    Route::get('/{token}', [ShareController::class, 'view'])->name('view');
    Route::post('/{token}/download', [ShareController::class, 'download'])->name('download');
});
