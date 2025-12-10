<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Process chat message from user
     */
    public function processMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'user_id' => 'nullable|integer',
            'session_id' => 'nullable|string',
        ]);

        $userInput = $request->input('message');
        $userId = $request->input('user_id');
        $sessionId = $request->input('session_id');

        // Process the input with the chatbot
        $response = $this->chatbotService->processInput($userInput, $userId, $sessionId);

        return response()->json([
            'success' => true,
            'response' => $response,
        ]);
    }

    /**
     * Get bot conversation history
     */
    public function getConversationHistory(Request $request, int $userId): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $history = $this->chatbotService->getConversationHistory($userId, $limit);

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Get common questions for FAQ section
     */
    public function getCommonQuestions(): JsonResponse
    {
        $questions = $this->chatbotService->getCommonQuestions();

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }

    /**
     * Get chatbot analytics for admin
     */
    public function getAnalytics(): JsonResponse
    {
        $analytics = $this->chatbotService->getAnalytics();

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Train the chatbot with new data
     */
    public function trainBot(Request $request): JsonResponse
    {
        $request->validate([
            'training_data' => 'required|array',
            'training_data.*.input' => 'required|string',
            'training_data.*.output' => 'required|string',
            'training_data.*.intent' => 'required|string',
        ]);

        $trainingData = $request->input('training_data');
        $success = $this->chatbotService->trainBot($trainingData);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Chatbot trained successfully' : 'Training failed',
        ]);
    }

    /**
     * Escalate to human agent
     */
    public function escalateToHuman(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'user_id' => 'nullable|integer',
        ]);

        $userInput = $request->input('message');
        $userId = $request->input('user_id');

        $escalation = $this->chatbotService->escalateToHuman($userInput, $userId);

        return response()->json([
            'success' => true,
            'escalation' => $escalation,
        ]);
    }

    /**
     * Webhook endpoint for external chat platforms
     */
    public function webhook(Request $request): JsonResponse
    {
        // Validate webhook signature if needed
        // Process the incoming message based on platform
        $platform = $request->header('X-Platform', 'generic');
        
        $message = match($platform) {
            'facebook' => $request->input('entry.0.messaging.0.message.text'),
            'telegram' => $request->input('message.text'),
            'whatsapp' => $request->input('messages.0.text.body'),
            default => $request->input('message'),
        };

        if (!$message) {
            return response()->json(['error' => 'No message found'], 400);
        }

        $response = $this->chatbotService->processInput($message);

        return response()->json([
            'reply' => $response['response'],
        ]);
    }

    /**
     * Get bot status
     */
    public function getStatus(): JsonResponse
    {
        return response()->json([
            'status' => 'online',
            'timestamp' => now(),
            'capabilities' => [
                'greeting' => true,
                'product_info' => true,
                'account_help' => true,
                'order_status' => true,
                'technical_support' => true,
                'faq' => true,
                'human_escalation' => true,
            ],
            'version' => '1.0.0',
        ]);
    }
}