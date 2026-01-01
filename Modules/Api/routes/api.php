<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\Api\V1\AuthController;

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

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
    });

    // API Key protected routes
    Route::middleware(['api.key'])->group(function () {
        Route::get('ping', function () {
            return response()->json(['message' => 'pong', 'tenant' => request()->attributes->get('tenant_id')]);
        });
        
        // Example resource routes protected by scope
        Route::middleware(['api.scope:read'])->group(function () {
            // Route::get('members', [MemberController::class, 'index']);
        });
    });
});
