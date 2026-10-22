<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\LoyaltyController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/firebase-login', [AuthController::class, 'firebaseLogin']);
Route::post('/firebase-register', [AuthController::class, 'firebaseRegister']);

Route::get('/image/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath, [
        'Access-Control-Allow-Origin' => '*'
    ]);
})->where('path', '.*');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}/tables', [EventController::class, 'tables']);
    Route::post('/events/{id}/book', [EventController::class, 'book']);

    // Orders
    Route::get('/categories', [OrderController::class, 'categories']);
    Route::get('/products', [OrderController::class, 'products']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'myOrders']);

    // Tickets
    Route::get('/tickets/check', [OrderController::class, 'checkTicket']);

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions/{id}/subscribe', [SubscriptionController::class, 'subscribe']);

    // Loyalty & Messages & Notifications
    Route::get('/loyalty', [LoyaltyController::class, 'index']);
    Route::get('/loyalty/details', [LoyaltyController::class, 'details']);
    Route::get('/loyalty/offers', [LoyaltyController::class, 'offers']);
    Route::post('/loyalty/offers/{id}/subscribe', [LoyaltyController::class, 'subscribe']);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
});

