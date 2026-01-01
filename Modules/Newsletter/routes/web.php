<?php

use Illuminate\Support\Facades\Route;
use Modules\Newsletter\Http\Controllers\NewsletterController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('newsletters', NewsletterController::class)->names('newsletter');
});
