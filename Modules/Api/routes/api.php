<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\Api\V1\AuthController;
use Modules\Api\Http\Controllers\Api\V1\MembersController;
use Modules\Api\Http\Controllers\Api\V1\EventsController;
use Modules\Api\Http\Controllers\Api\V1\PaymentsController;

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

Route::prefix('v1')->middleware(['api.key', 'api.rate', 'api.log'])->group(function () {
    // Auth
    Route::post('auth/login', [AuthController::class, 'login'])->withoutMiddleware(['api.key']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me', [AuthController::class, 'me']);
    
    // Members
    Route::get('members/categories', [MembersController::class, 'categories']);
    Route::get('members/{id}/payments', [MembersController::class, 'payments']);
    Route::apiResource('members', MembersController::class);
    
    // Events
    Route::get('events/{id}/registrations', [EventsController::class, 'registrations']);
    Route::post('events/{id}/register', [EventsController::class, 'register']);
    Route::delete('events/{id}/registrations/{user_id}', [EventsController::class, 'unregister']);
    Route::apiResource('events', EventsController::class);
    
    // Payments
    Route::get('invoices', [PaymentsController::class, 'invoices']);
    Route::get('invoices/{id}', [PaymentsController::class, 'invoice']);
    Route::get('invoices/{id}/pdf', [PaymentsController::class, 'invoicePdf']);
    Route::apiResource('payments', PaymentsController::class)->only(['index', 'show', 'store']);
});
