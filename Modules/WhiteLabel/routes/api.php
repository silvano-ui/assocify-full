<?php

use Illuminate\Support\Facades\Route;
use Modules\WhiteLabel\Http\Controllers\WhiteLabelController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('whitelabels', WhiteLabelController::class)->names('whitelabel');
});
