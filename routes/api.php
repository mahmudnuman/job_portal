<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ApplicationController;


Route::get('/test-mail', function () {
    Mail::raw('This is a Mailtrap test email from Job Portal!', function ($message) {
        $message->to('mahmudnuman@gmail.com')->subject('Test Email');
    });

    return 'Test email sent!';
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/listings', [ListingController::class, 'index']); // List all jobs
// Protected routes - must be logged in with JWT
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/listings/{id}', [ListingController::class, 'show']); 
    Route::post('/listings/{id}/apply', [ApplicationController::class, 'apply']);
});
