<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel AI API with Ollama Mistral',
        'version' => '1.0.0',
        'ai_model' => 'Mistral (via Ollama)',
        'endpoints' => [
            'Authentication' => [
                'POST /api/register' => 'Register a new user',
                'POST /api/login' => 'Login user',
                'POST /api/logout' => 'Logout user (requires auth)',
                'GET /api/me' => 'Get authenticated user (requires auth)',
            ],
            'Chat' => [
                'POST /api/chat' => 'Send message to AI (guest or authenticated)',
                'POST /api/chat (with session_id)' => 'Continue existing session (requires auth)',
            ],
            'Sessions' => [
                'GET /api/sessions' => 'Get all sessions (requires auth)',
                'GET /api/sessions/{id}' => 'Get session history (requires auth)',
                'DELETE /api/sessions/{id}' => 'Delete specific session (requires auth)',
                'DELETE /api/sessions' => 'Delete all sessions (requires auth)',
            ],
        ],
        'documentation' => 'See README.md for full documentation',
    ]);
});
