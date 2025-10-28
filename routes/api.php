<?php


use Illuminate\Support\Facades\Route;




use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\TicketController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    
    Route::post('/events/{event_id}/tickets', [TicketController::class, 'store']);
    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);

    
    Route::middleware('prevent.double.booking')->group(function () {
        Route::post('/tickets/{ticket}/bookings', [BookingController::class, 'store'])
            ->name('bookings.store');
    });
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    
    Route::post('/bookings/{id}/payment', [PaymentController::class, 'store']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
});
