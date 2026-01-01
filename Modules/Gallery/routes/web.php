<?php

use Illuminate\Support\Facades\Route;
use Modules\Gallery\Http\Controllers\GalleryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('galleries', GalleryController::class)->names('gallery');
});
