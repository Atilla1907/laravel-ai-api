<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/chat', [ChatbotController::class, 'chat']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/sessions', [ChatbotController::class, 'getSessions']);
    Route::get('/sessions/{sessionId}', [ChatbotController::class, 'getSessionHistory']);
    Route::delete('/sessions/{sessionId}', [ChatbotController::class, 'deleteSession']);
    Route::delete('/sessions', [ChatbotController::class, 'deleteAllSessions']);
});
