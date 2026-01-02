<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->prefix('reports')->name('api.reports.')->group(function () {
    Route::get('/types', [ReportController::class, 'types'])->name('types');
    Route::get('/columns/{dataSource}', [ReportController::class, 'columns'])->name('columns');
    Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
    Route::get('/{report}/status', [ReportController::class, 'status'])->name('status');
    Route::post('/{report}/share', [ReportController::class, 'share'])->name('share');
});
