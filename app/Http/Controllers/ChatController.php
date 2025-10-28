<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\VirtualAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $vaService;

    public function __construct(VirtualAssistantService $vaService)
    {
        $this->vaService = $vaService;
    }

    /**
     * Display the chat interface
     */
    public function index()
    {
        return view('chat.index');
    }

    /**
     * Send a message and get AI response
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        $userMessage = trim($request->message);

        // Save user message
        ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => $userMessage,
        ]);

        // Get conversation history for context
        $conversationHistory = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->take(20) // Last 20 messages for context
            ->get(['sender', 'message'])
            ->toArray();

        // Generate AI response
        $aiResponse = $this->vaService->generateChatResponse($userMessage, $conversationHistory);

        // Save AI response
        ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'ai',
            'message' => $aiResponse,
        ]);

        return response()->json([
            'success' => true,
            'response' => $aiResponse
        ]);
    }

    /**
     * Get chat history for AJAX loading
     */
    public function getHistory()
    {
        $messages = ChatMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->take(50) // Limit to last 50 messages
            ->get(['sender', 'message', 'created_at'])
            ->map(function ($message) {
                return [
                    'sender' => $message->sender,
                    'message' => $message->message,
                    'timestamp' => $message->created_at->format('M d, H:i')
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
}
