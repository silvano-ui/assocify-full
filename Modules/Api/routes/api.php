<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\Api\V1\AuthController;
use Modules\Api\Http\Controllers\Api\V1\MembersController;
use Modules\Api\Http\Controllers\Api\V1\EventsController;
use Modules\Api\Http\Controllers\Api\V1\PaymentsController;
use Modules\Api\Http\Controllers\Api\V1\DocumentsController;
use Modules\Api\Http\Controllers\Api\V1\GalleryController;
use Modules\Api\Http\Controllers\Api\V1\ChatController;
use Modules\Api\Http\Controllers\Api\V1\NewsletterController;

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

    // Documents
    Route::get('documents/categories', [DocumentsController::class, 'categories']);
    Route::get('documents/{id}/download', [DocumentsController::class, 'download']);
    Route::apiResource('documents', DocumentsController::class);

    // Gallery
    Route::apiResource('albums', GalleryController::class)->only(['index', 'show']);
    // Media routes manually defined to avoid conflict in controller if using same controller for two resources
    Route::get('media', [GalleryController::class, 'mediaIndex']);
    Route::get('media/{id}', [GalleryController::class, 'mediaShow']);
    Route::post('media', [GalleryController::class, 'mediaStore']);
    Route::delete('media/{id}', [GalleryController::class, 'mediaDestroy']);

    // Chat
    Route::get('conversations', [ChatController::class, 'index']);
    Route::get('conversations/{id}/messages', [ChatController::class, 'messages']);
    Route::post('conversations/{id}/messages', [ChatController::class, 'sendMessage']);
    Route::post('conversations', [ChatController::class, 'store']);

    // Newsletter
    Route::get('newsletter/lists', [NewsletterController::class, 'lists']);
    Route::get('newsletter/subscribers', [NewsletterController::class, 'subscribers']);
    Route::post('newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);
});
