<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class ChatbotController extends Controller
{
    /**
     * Chat with the LLM (Ollama Mistral)
     * 
     * Handles three scenarios:
     * 1. Guest user (no authentication) - simple chat without history
     * 2. Authenticated user with new session - creates new session_id
     * 3. Authenticated user with existing session - continues conversation with history
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'session_id' => 'nullable|uuid',
        ]);

        $user = $request->user();
        $sessionId = $request->input('session_id');

        // Scenario 1: Guest user (no authentication)
        if (!$user) {
            return $this->handleGuestChat($request->message);
        }

        // Scenario 2: Authenticated user with new session
        if (!$sessionId) {
            return $this->handleNewSession($user, $request->message);
        }

        // Scenario 3: Authenticated user with existing session
        return $this->handleExistingSession($user, $sessionId, $request->message);
    }

    /**
     * Handle chat for guest users (no authentication)
     */
    private function handleGuestChat(string $message)
    {
        $response = Http::post('http://localhost:11434/api/generate', [
            'model' => 'mistral',
            'prompt' => $message,
            'stream' => false
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to get response from LLM',
                'message' => 'Make sure Ollama is running with: ollama serve'
            ], 500);
        }

        $data = $response->json();
        
        return response()->json([
            'response' => $data['response'] ?? 'No response from LLM',
            'message' => 'Guest chat - no history saved',
        ]);
    }

    /**
     * Handle chat for authenticated user with new session
     */
    private function handleNewSession($user, string $message)
    {
        // Generate new UUID for the session
        $sessionId = (string) Uuid::uuid4();
        
        // Format message for LLM
        $messages = [
            ['role' => 'user', 'content' => $message]
        ];

        // Send to LLM
        $response = Http::post('http://localhost:11434/api/chat', [
            'model' => 'mistral',
            'messages' => $messages,
            'stream' => false,
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to get response from LLM',
                'message' => 'Make sure Ollama is running with: ollama serve'
            ], 500);
        }

        $data = $response->json();
        $botResponse = $data['message']['content'] ?? 'No response from LLM';

        // Save to database
        ChatHistory::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'user_message' => $message,
            'bot_response' => $botResponse,
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'response' => $botResponse,
            'message' => 'New session created',
        ]);
    }

    /**
     * Handle chat for authenticated user with existing session
     */
    private function handleExistingSession($user, string $sessionId, string $message)
    {
        // Query for previous messages in this session
        $previousMessages = ChatHistory::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($chat) => [
                ['role' => 'user', 'content' => $chat->user_message],
                ['role' => 'assistant', 'content' => $chat->bot_response],
            ])
            ->flatten(1)
            ->toArray();

        // Add current message to the conversation
        $messages = array_merge($previousMessages, [
            ['role' => 'user', 'content' => $message]
        ]);

        // Send to LLM with full conversation history
        $response = Http::post('http://localhost:11434/api/chat', [
            'model' => 'mistral',
            'messages' => $messages,
            'stream' => false,
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to get response from LLM',
                'message' => 'Make sure Ollama is running with: ollama serve'
            ], 500);
        }

        $data = $response->json();
        $botResponse = $data['message']['content'] ?? 'No response from LLM';

        // Save to database
        ChatHistory::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'user_message' => $message,
            'bot_response' => $botResponse,
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'response' => $botResponse,
            'message' => 'Continuing existing session',
            'message_count' => count($previousMessages) / 2 + 1,
        ]);
    }

    /**
     * Get all sessions for the authenticated user
     */
    public function getSessions(Request $request)
    {
        $sessions = ChatHistory::where('user_id', $request->user()->id)
            ->select('session_id')
            ->selectRaw('COUNT(*) as message_count')
            ->selectRaw('MIN(created_at) as started_at')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->groupBy('session_id')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'sessions' => $sessions,
        ]);
    }

    /**
     * Get chat history for a specific session
     */
    public function getSessionHistory(Request $request, string $sessionId)
    {
        $history = ChatHistory::where('user_id', $request->user()->id)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($history->isEmpty()) {
            return response()->json([
                'error' => 'Session not found',
            ], 404);
        }

        return response()->json([
            'session_id' => $sessionId,
            'history' => $history,
            'message_count' => $history->count(),
        ]);
    }

    /**
     * Delete a specific session
     */
    public function deleteSession(Request $request, string $sessionId)
    {
        $deleted = ChatHistory::where('user_id', $request->user()->id)
            ->where('session_id', $sessionId)
            ->delete();

        return response()->json([
            'message' => 'Session deleted successfully',
            'deleted_messages' => $deleted,
        ]);
    }

    /**
     * Delete all sessions for the authenticated user
     */
    public function deleteAllSessions(Request $request)
    {
        $deleted = ChatHistory::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'All sessions deleted successfully',
            'deleted_messages' => $deleted,
        ]);
    }
}
