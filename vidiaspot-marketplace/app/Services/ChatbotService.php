<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ChatbotService
{
    protected $openaiApiKey;
    protected $intentClassifier;
    protected $conversationMemory;
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
        $this->openaiApiKey = env('OPENAI_API_KEY');
        $this->intentClassifier = $this->initializeIntentClassifier();
    }

    /**
     * Initialize intent classifier with common intents
     */
    protected function initializeIntentClassifier(): array
    {
        return [
            'greeting' => [
                'patterns' => [
                    'hello', 'hi', 'hey', 'good morning', 'good afternoon', 
                    'good evening', 'morning', 'afternoon', 'evening', 
                    'greetings', 'sup', 'whats up', 'howdy'
                ],
                'responses' => [
                    "Hello! How can I help you today?",
                    "Hi there! How can I assist you?",
                    "Greetings! What can I do for you?",
                    "Hey! Need help with something?"
                ]
            ],
            'farewell' => [
                'patterns' => [
                    'goodbye', 'good bye', 'bye', 'see you', 'talk to you later',
                    'see ya', 'cya', 'have a good day', 'thanks bye', 'thank you goodbye'
                ],
                'responses' => [
                    "Goodbye! Feel free to reach out if you need anything else.",
                    "See you later! Have a great day!",
                    "Thank you for chatting with me. Have a wonderful day!",
                    "Take care! Don't hesitate to come back if you have more questions."
                ]
            ],
            'help' => [
                'patterns' => [
                    'help', 'support', 'assist', 'need help', 'can you help',
                    'how do i', 'what can i do', 'how to', 'tutorial', 'guide',
                    'instructions', 'support', 'customer service'
                ],
                'responses' => [
                    "I'm here to help! You can ask me about our products, pricing, shipping, returns, or account issues.",
                    "I can assist with product information, order status, account management, and common questions.",
                    "Sure, I can provide information about our marketplace, selling process, payment methods, or policies."
                ]
            ],
            'product_info' => [
                'patterns' => [
                    'product', 'item', 'what is', 'tell me about', 'information about',
                    'details about', 'specs', 'specifications', 'features', 'price', 
                    'cost', 'how much', 'pricing', 'buy', 'purchase'
                ],
                'responses' => [
                    "To get product information, please provide the product name or ID.",
                    "You can search for products using our search bar or browse categories.",
                    "For specific product details, visit the product page or contact the seller directly."
                ]
            ],
            'account' => [
                'patterns' => [
                    'account', 'profile', 'settings', 'login', 'sign in', 'register',
                    'signup', 'sign up', 'password', 'forgot password', 'reset password',
                    'verification', 'email', 'change', 'update', 'my account'
                ],
                'responses' => [
                    "For account issues, please visit the account settings page or contact support.",
                    "You can update your profile information and security settings in your account dashboard.",
                    "If you're having trouble with login, try resetting your password or contacting support."
                ]
            ],
            'order' => [
                'patterns' => [
                    'order', 'purchase', 'shipment', 'shipping', 'delivery', 'tracking',
                    'tracking number', 'status', 'cancel', 'return', 'refund', 'invoice',
                    'payment', 'receipt', 'buy'
                ],
                'responses' => [
                    "For order inquiries, please check your account dashboard for order history and status.",
                    "You can track your order using the tracking number provided in your confirmation email.",
                    "For returns or refunds, please follow the process in our help center."
                ]
            ],
            'technical_issue' => [
                'patterns' => [
                    'not working', 'broken', 'error', 'issue', 'problem', 'bug',
                    'doesn\'t work', 'can\'t access', 'site down', 'slow', 'crash',
                    'won\'t load', 'loading', 'timeout', 'freeze', 'malfunction'
                ],
                'responses' => [
                    "I'm sorry you're experiencing technical issues. Try refreshing the page or clearing your browser cache.",
                    "If the problem persists, please contact our technical support team with details about the issue.",
                    "For urgent technical issues, please submit a ticket through our support portal."
                ]
            ],
            'default' => [
                'responses' => [
                    "I'm not sure I understand. Can you rephrase your question?",
                    "Could you provide more details about what you need help with?",
                    "I'm here to help with common questions about our marketplace, products, or accounts.",
                    "You might want to check our FAQ section for common questions."
                ]
            ]
        ];
    }

    /**
     * Process user input and generate bot response
     */
    public function processInput(string $userInput, int $userId = null, string $sessionId = null): array
    {
        // Clean and normalize input
        $cleanInput = strtolower(trim($userInput));
        $cleanInput = preg_replace('/[^\w\s]/', ' ', $cleanInput);

        // Check for cached response
        $cacheKey = "chatbot_response:" . sha1($cleanInput . $userId . $sessionId);
        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // Classify intent
        $intent = $this->classifyIntent($cleanInput);
        
        // Generate response based on intent
        $responseText = $this->generateResponse($intent);

        // Create a more sophisticated response based on context
        $response = [
            'intent' => $intent['type'],
            'response' => $responseText,
            'confidence' => $intent['confidence'],
            'follow_up_suggestions' => $this->getFollowUpSuggestions($intent['type']),
            'needs_human_assistance' => $this->needsHumanAssistance($intent),
        ];

        // Store in cache for 10 minutes
        $this->redisService->put($cacheKey, $response, 600);

        // Store conversation in history if user ID provided
        if ($userId) {
            $this->storeConversation($userInput, $responseText, $userId, $sessionId);
        }

        return $response;
    }

    /**
     * Classify user input intent
     */
    protected function classifyIntent(string $input): array
    {
        $words = explode(' ', $input);
        
        // Calculate scores for each intent
        $scores = [];
        $maxScore = 0;
        $bestIntent = 'default';

        foreach ($this->intentClassifier as $intentType => $intentData) {
            if ($intentType === 'default') continue; // Skip default for scoring

            $score = 0;
            foreach ($words as $word) {
                if (in_array($word, $intentData['patterns'])) {
                    $score++;
                }
            }

            $scores[$intentType] = $score;
            if ($score > $maxScore) {
                $maxScore = $score;
                $bestIntent = $intentType;
            }
        }

        // Calculate confidence level
        $totalWords = count($words);
        $confidence = $totalWords > 0 ? $maxScore / $totalWords : 0;
        $confidence = min($confidence, 1.0); // Cap at 1.0

        // If no clear intent detected, use default
        if ($maxScore === 0) {
            $bestIntent = 'default';
            $confidence = 0.0;
        }

        return [
            'type' => $bestIntent,
            'confidence' => $confidence,
            'details' => [
                'detected_words' => [],
                'confidence_details' => $scores,
            ]
        ];
    }

    /**
     * Generate response based on intent
     */
    protected function generateResponse(array $intent): string
    {
        $intentType = $intent['type'];
        $responses = $this->intentClassifier[$intentType]['responses'] ?? 
                    $this->intentClassifier['default']['responses'];

        // Randomly pick a response
        return $responses[array_rand($responses)];
    }

    /**
     * Get follow-up suggestions based on intent
     */
    protected function getFollowUpSuggestions(string $intentType): array
    {
        $suggestions = [
            'greeting' => [
                'Tell me about your marketplace',
                'How do I list items?',
                'What are your fees?',
            ],
            'product_info' => [
                'Search for products',
                'Browse categories',
                'Contact seller',
            ],
            'account' => [
                'Update profile',
                'Change password',
                'Manage privacy settings',
            ],
            'order' => [
                'Check order status',
                'Return policy',
                'Shipping options',
            ],
            'help' => [
                'View FAQ',
                'Contact support',
                'Knowledge base',
            ],
        ];

        return $suggestions[$intentType] ?? [
            'Need more help?',
            'Check our FAQ',
            'Contact human agent',
        ];
    }

    /**
     * Determine if question requires human assistance
     */
    protected function needsHumanAssistance(array $intent): bool
    {
        // High complexity or low confidence indicates need for human
        return $intent['confidence'] < 0.3;
    }

    /**
     * Store conversation for learning and analytics
     */
    protected function storeConversation(string $userInput, string $botResponse, int $userId, ?string $sessionId): void
    {
        // In a real implementation, you would store this in a database
        // For demo purposes, we'll log basic info
        Log::info('Chatbot conversation', [
            'user_id' => $userId,
            'session_id' => $sessionId,
            'user_input' => $userInput,
            'bot_response' => $botResponse,
            'timestamp' => now(),
        ]);
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory(int $userId, int $limit = 10): array
    {
        // This would typically fetch from a database
        // For now, returns mock data
        return [
            [
                'id' => 1,
                'user_message' => 'Hello, I need help with my order',
                'bot_response' => 'I can help with order inquiries. Please provide your order ID or account details.',
                'timestamp' => now()->subMinutes(5),
                'resolved' => false,
            ],
            [
                'id' => 2,
                'user_message' => 'How do I update my profile?',
                'bot_response' => 'You can update your profile by going to Account Settings > Profile > Edit Information.',
                'timestamp' => now()->subMinutes(10),
                'resolved' => true,
            ],
        ];
    }

    /**
     * Get common questions for FAQ
     */
    public function getCommonQuestions(): array
    {
        return [
            [
                'question' => 'How do I list an item for sale?',
                'answer' => 'To list an item, click the "Post Ad" button, fill in the details, add photos, and submit. Your ad will be reviewed and published shortly.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept various payment methods including credit cards, bank transfers, mobile money, and other local payment options depending on your region.'
            ],
            [
                'question' => 'How long does shipping take?',
                'answer' => 'Shipping times vary by location and seller. Usually, it takes 2-7 business days. Specific times are listed in the product description.'
            ],
            [
                'question' => 'Can I return an item?',
                'answer' => 'Return policies depend on the seller. Check the individual product page for specific return conditions and timeframes.'
            ],
            [
                'question' => 'How do I contact a seller?',
                'answer' => 'Use the "Contact Seller" button on the product page to message them directly through our platform.'
            ],
        ];
    }

    /**
     * Train the bot with new patterns/responses
     */
    public function trainBot(array $trainingData): bool
    {
        // In a real implementation, this would update the intent classifier
        // For now, just return success
        Log::info('Training chatbot with new data', $trainingData);
        return true;
    }

    /**
     * Get bot analytics
     */
    public function getAnalytics(): array
    {
        // Return mock analytics data
        // In a real system, this would aggregate data from database
        return [
            'total_conversations' => 1250,
            'avg_response_time' => 0.5, // seconds
            'resolution_rate' => 0.72, // 72% resolved without human
            'top_intents' => [
                ['intent' => 'greeting', 'count' => 210],
                ['intent' => 'product_info', 'count' => 185],
                ['intent' => 'account', 'count' => 142],
                ['intent' => 'help', 'count' => 128],
                ['intent' => 'order', 'count' => 98],
            ],
            'satisfaction_rate' => 0.85, // based on feedback
            'human_handoff_rate' => 0.28, // 28% require human
        ];
    }

    /**
     * Handle complex queries with OpenAI (fallback)
     */
    public function handleComplexQuery(string $userInput): ?string
    {
        if (!$this->openaiApiKey) {
            return null; // OpenAI not configured
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->openaiApiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful customer service assistant for a Nigerian marketplace called VidiaSpot. Answer questions politely and professionally, focusing on marketplace topics like products, orders, accounts, payments, and general support. Keep responses concise and helpful.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $userInput
                    ]
                ],
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Escalate to human agent
     */
    public function escalateToHuman(string $userInput, int $userId = null): array
    {
        // Log the escalation
        Log::info('Chatbot escalation to human agent', [
            'user_id' => $userId,
            'input' => $userInput,
            'timestamp' => now(),
        ]);

        // In a real system, this would trigger a notification to human support
        // For now, return a canned response
        return [
            'escalated' => true,
            'message' => 'I\'ve escalated your query to our support team. Someone will assist you shortly. You can also contact our support directly at support@vidiaspot.com or call +234-XXX-XXXX.',
            'ticket_created' => true,
            'expected_response_time' => 'within 24 hours',
        ];
    }
}