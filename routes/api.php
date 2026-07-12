<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ItineraryController;

// Auth routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/google/redirect', [AuthController::class, 'googleRedirect']);
    Route::get('/google/callback', [AuthController::class, 'googleCallback']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Trips
    Route::get('/trips', [TripController::class, 'index']);
    Route::post('/trips', [TripController::class, 'store']);
    Route::get('/trips/{id}', [TripController::class, 'show']);
    Route::put('/trips/{id}', [TripController::class, 'update']);
    Route::delete('/trips/{id}', [TripController::class, 'destroy']);
    Route::post('/trips/join', [TripController::class, 'join']);
    Route::get('/trips/{id}/members', [TripController::class, 'members']);
    Route::delete('/trips/{id}/members/{userId}', [TripController::class, 'removeMember']);

    // Expenses
    Route::get('/trips/{id}/expenses', [ExpenseController::class, 'index']);
    Route::post('/trips/{id}/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
    Route::get('/trips/{id}/expenses/summary', [ExpenseController::class, 'summary']);

    // Itinerary
    Route::get('/trips/{id}/itinerary', [ItineraryController::class, 'index']);
    Route::post('/trips/{id}/itinerary', [ItineraryController::class, 'store']);
    Route::put('/itinerary/{id}', [ItineraryController::class, 'update']);
    Route::delete('/itinerary/{id}', [ItineraryController::class, 'destroy']);
    Route::put('/trips/{id}/itinerary/reorder', [ItineraryController::class, 'reorder']);
});
