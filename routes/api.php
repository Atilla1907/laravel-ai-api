<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Chat endpoint - works for both guest and authenticated users
Route::post('/chat', [ChatbotController::class, 'chat']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Session management routes
    Route::get('/sessions', [ChatbotController::class, 'getSessions']);
    Route::get('/sessions/{session_id}', [ChatbotController::class, 'getSessionHistory']);
    Route::delete('/sessions/{session_id}', [ChatbotController::class, 'deleteSession']);
    Route::delete('/sessions', [ChatbotController::class, 'deleteAllSessions']);
});
