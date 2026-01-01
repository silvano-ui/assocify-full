<?php

use Illuminate\Support\Facades\Route;
use Modules\WhiteLabel\Http\Controllers\WhiteLabelController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('whitelabels', WhiteLabelController::class)->names('whitelabel');
});
