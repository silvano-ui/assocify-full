<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\ApiController;
use Illuminate\Support\Facades\File;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('apis', ApiController::class)->names('api');

    Route::get('api/documentation', function () {
        return view('api::swagger');
    })->name('api.documentation');

    Route::get('api/docs/openapi.yaml', function () {
        $yamlPath = module_path('Api', 'Docs/openapi.yaml');
        if (File::exists($yamlPath)) {
            return response()->file($yamlPath, ['Content-Type' => 'text/yaml']);
        }
        abort(404, 'OpenAPI specification not found.');
    })->name('api.docs.yaml');
});
