<?php

namespace App\Services;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AI\AIService;
use App\Services\ChatbotService;
use Illuminate\Support\Facades\Auth;

class AICustomerServiceAvatar
{
    protected $aiService;
    protected $chatbotService;

    public function __construct(AIService $aiService, ChatbotService $chatbotService)
    {
        $this->aiService = $aiService;
        $this->chatbotService = $chatbotService;
    }

    public function processCustomerQuery($query, $userId = null, $context = [])
    {
        $userId = $userId ?: Auth::id();
        
        // Enhance the query with context about the user and their history
        $enhancedQuery = $this->enhanceQueryWithUserContext($query, $userId, $context);
        
        // Generate AI response based on the enhanced query
        $aiResponse = $this->aiService->generateResponse([
            'prompt' => $enhancedQuery,
            'user_id' => $userId,
            'context' => $context,
            'model_type' => 'customer_service',
        ]);
        
        // Log the interaction
        $this->logInteraction($query, $aiResponse, $userId);
        
        return [
            'response' => $aiResponse['content'] ?? 'I understand your query. Let me help you with that.',
            'confidence' => $aiResponse['confidence'] ?? 0.8,
            'follow_up_questions' => $aiResponse['follow_up_questions'] ?? [],
            'related_topics' => $aiResponse['related_topics'] ?? [],
            'action_items' => $aiResponse['action_items'] ?? [],
        ];
    }

    public function createAvatarSession($userId = null, $sessionData = [])
    {
        $userId = $userId ?: Auth::id();
        
        // Create a conversation record for tracking the session
        $conversation = Conversation::create([
            'user_id' => $userId,
            'title' => 'AI Customer Service Session - ' . now(),
            'type' => 'customer_service',
            'metadata' => $sessionData,
            'status' => 'active',
        ]);

        return (object)[
            'session_id' => $conversation->id,
            'session_token' => $this->generateSessionToken($conversation->id),
            'welcome_message' => $this->getWelcomeMessage($userId),
        ];
    }

    public function continueAvatarSession($sessionId, $query, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $conversation = Conversation::find($sessionId);
        
        if (!$conversation || $conversation->user_id !== $userId) {
            throw new \Exception('Invalid session');
        }

        // Add user message to the conversation
        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'sender_type' => 'user',
            'content' => $query,
            'message_type' => 'text',
            'metadata' => ['source' => 'customer_service_avatar'],
        ]);

        // Generate AI response based on the conversation history
        $context = $this->buildContextFromConversation($conversation, $query);
        $aiResponse = $this->processCustomerQuery($query, $userId, $context);
        
        // Add AI response to the conversation
        $aiMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null, // System message
            'sender_type' => 'system',
            'content' => $aiResponse['response'],
            'message_type' => 'text',
            'metadata' => [
                'confidence' => $aiResponse['confidence'],
                'follow_up_questions' => $aiResponse['follow_up_questions'],
                'action_items' => $aiResponse['action_items'],
            ],
        ]);

        return [
            'response' => $aiResponse,
            'message_id' => $aiMessage->id,
        ];
    }

    public function getPersonalizedAvatarResponse($userId = null, $query, $avatarType = 'text')
    {
        $userId = $userId ?: Auth::id();
        
        // Get user profile and preferences for personalization
        $user = User::with(['preferences', 'orders', 'interactions'])->find($userId);
        
        $context = [
            'user_profile' => [
                'name' => $user->name,
                'preferences' => $user->preferences,
                'recent_orders' => $user->orders()->limit(5)->get()->toArray(),
                'interaction_history' => $user->interactions()->limit(10)->get()->toArray(),
            ],
            'avatar_type' => $avatarType,
        ];
        
        return $this->processCustomerQuery($query, $userId, $context);
    }

    public function getAvatarCapabilities()
    {
        return [
            'text_responses' => true,
            'voice_synthesis' => true,
            'visual_avatar' => true,
            'multi_language' => true,
            'contextual_memory' => true,
            'sentiment_analysis' => true,
            'escalation_handling' => true,
            'order_tracking' => true,
            'product_recommendations' => true,
            'faq_handling' => true,
        ];
    }

    public function handleEscalation($sessionId, $issueType, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        // Mark conversation as escalated
        $conversation = Conversation::find($sessionId);
        if ($conversation && $conversation->user_id === $userId) {
            $conversation->update([
                'status' => 'escalated',
                'metadata' => array_merge($conversation->metadata ?? [], [
                    'escalation_type' => $issueType,
                    'escalated_at' => now(),
                ]),
            ]);
        }

        // Trigger notification to human support agent
        $this->notifyHumanSupport($userId, $issueType, $sessionId);
        
        return [
            'status' => 'escalated',
            'message' => 'Your issue has been escalated to a human support agent. They will contact you shortly.',
            'estimated_wait_time' => '2-5 minutes',
        ];
    }

    public function getAvatarPersonality($type = 'default')
    {
        $personalities = [
            'friendly' => [
                'tone' => 'friendly and approachable',
                'style' => 'conversational',
                'traits' => ['helpful', 'empathetic', 'patient', 'positive'],
            ],
            'professional' => [
                'tone' => 'professional and formal',
                'style' => 'direct and informative',
                'traits' => ['knowledgeable', 'respectful', 'efficient', 'reliable'],
            ],
            'casual' => [
                'tone' => 'casual and relaxed',
                'style' => 'informal',
                'traits' => ['friendly', 'chatty', 'informal', 'fun'],
            ],
            'default' => [
                'tone' => 'professional yet friendly',
                'style' => 'helpful and informative',
                'traits' => ['helpful', 'professional', 'patient', 'knowledgeable'],
            ],
        ];

        return $personalities[$type] ?? $personalities['default'];
    }

    public function analyzeSentiment($text)
    {
        // Use the existing AI service for sentiment analysis
        return $this->aiService->analyzeSentiment($text);
    }

    public function generateAvatarVoiceResponse($text, $voiceSettings = [])
    {
        // This would interface with a text-to-speech service
        // For now, return the text as-is
        return [
            'text' => $text,
            'voice_url' => null, // In a real implementation, this would be a generated audio URL
            'voice_settings' => $voiceSettings,
        ];
    }

    public function generateAvatarVisualResponse($text, $userQuery, $userId = null)
    {
        // Generate parameters for a visual avatar animation
        $userId = $userId ?: Auth::id();
        
        // Determine avatar expression based on sentiment of the response
        $sentiment = $this->analyzeSentiment($text);
        
        $expression = $this->determineAvatarExpression($sentiment, $userQuery);
        $greeting = $this->determineAvatarGreeting($userQuery, $userId);
        
        return [
            'text' => $text,
            'avatar_expression' => $expression,
            'greeting' => $greeting,
            'animation_sequence' => $this->createAnimationSequence($expression),
            'visual_context' => [
                'user_sentiment' => $sentiment,
                'avatar_personality' => $this->getAvatarPersonality(),
            ],
        ];
    }

    private function enhanceQueryWithUserContext($query, $userId, $context = [])
    {
        $user = User::find($userId);
        
        $enhancedQuery = "Customer Query: " . $query . "\n";
        $enhancedQuery .= "Customer Name: " . ($user->name ?? 'Unknown') . "\n";
        $enhancedQuery .= "Customer ID: " . $userId . "\n";
        
        if (isset($context['order_id'])) {
            $enhancedQuery .= "Related Order ID: " . $context['order_id'] . "\n";
        }
        
        if (isset($context['product_id'])) {
            $enhancedQuery .= "Related Product ID: " . $context['product_id'] . "\n";
        }
        
        if (isset($context['previous_queries'])) {
            $enhancedQuery .= "Previous Queries in Session: " . implode(', ', $context['previous_queries']) . "\n";
        }
        
        $enhancedQuery .= "Current Time: " . now() . "\n";
        $enhancedQuery .= "Expected Response Format: Helpful, clear, and friendly";
        
        return $enhancedQuery;
    }

    private function getWelcomeMessage($userId)
    {
        $user = User::find($userId);
        
        $timeOfDay = $this->getTimeOfDay();
        
        return "Hello " . ($user->name ?? 'there') . "! I'm your AI customer service avatar. " .
               "I'm here to help you with any questions or issues you might have. " .
               "How can I assist you this {$timeOfDay}?";
    }

    private function getTimeOfDay()
    {
        $hour = now()->hour;
        
        if ($hour >= 5 && $hour < 12) {
            return 'morning';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'afternoon';
        } elseif ($hour >= 17 && $hour < 21) {
            return 'evening';
        } else {
            return 'night';
        }
    }

    private function logInteraction($query, $response, $userId)
    {
        // Log the interaction for analytics and improvement
        \App\Models\CustomerInteraction::create([
            'user_id' => $userId,
            'type' => 'ai_customer_service',
            'query' => $query,
            'response' => is_array($response) ? json_encode($response) : $response,
            'metadata' => [
                'timestamp' => now(),
                'session_id' => request()->session()->getId() ?? null,
            ],
        ]);
    }

    private function buildContextFromConversation($conversation, $newQuery)
    {
        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentQueries = [];
        foreach ($messages as $message) {
            if ($message->sender_type === 'user') {
                $recentQueries[] = $message->content;
            }
        }
        
        return [
            'previous_queries' => array_reverse($recentQueries),
            'conversation_length' => $conversation->messages->count(),
        ];
    }

    private function generateSessionToken($conversationId)
    {
        return hash('sha256', $conversationId . time() . config('app.key'));
    }

    private function notifyHumanSupport($userId, $issueType, $sessionId)
    {
        // In a real implementation, this would send a notification to a support system
        // For now, we'll just log it
        \Log::info("AI Customer Service Escalation", [
            'user_id' => $userId,
            'issue_type' => $issueType,
            'session_id' => $sessionId,
            'escalated_at' => now(),
        ]);
    }

    private function determineAvatarExpression($sentiment, $query)
    {
        if ($sentiment['sentiment'] === 'negative') {
            return 'concerned';
        } elseif ($sentiment['sentiment'] === 'positive') {
            return 'happy';
        } else {
            return 'neutral';
        }
    }

    private function determineAvatarGreeting($query, $userId)
    {
        if (stripos($query, 'help') !== false || stripos($query, 'issue') !== false) {
            return 'helpful';
        } elseif (stripos($query, 'thank') !== false) {
            return 'appreciative';
        } else {
            return 'standard';
        }
    }

    private function createAnimationSequence($expression)
    {
        $sequences = [
            'happy' => ['smile', 'nod', 'wave'],
            'concerned' => ['frown', 'think', 'empathize'],
            'neutral' => ['listen', 'think', 'respond'],
            'appreciative' => ['smile', 'thank', 'wink'],
            'helpful' => ['think', 'explain', 'assist'],
        ];

        return $sequences[$expression] ?? $sequences['neutral'];
    }
}