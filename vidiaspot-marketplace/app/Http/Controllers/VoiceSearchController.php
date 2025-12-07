<?php

namespace App\Http\Controllers;

use App\Services\ElasticsearchService;
use App\Services\SpeechRecognitionService;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoiceSearchController extends Controller
{
    protected ElasticsearchService $elasticsearchService;
    protected SpeechRecognitionService $speechRecognitionService;
    protected SearchService $searchService;

    public function __construct(
        ElasticsearchService $elasticsearchService,
        SpeechRecognitionService $speechRecognitionService,
        SearchService $searchService
    ) {
        $this->elasticsearchService = $elasticsearchService;
        $this->speechRecognitionService = $speechRecognitionService;
        $this->searchService = $searchService;
    }

    /**
     * Transcribe and search from audio input
     */
    public function voiceSearch(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,ogg|max:10240', // 10MB max
        ]);

        try {
            // Get the audio file
            $audioFile = $request->file('audio');

            // Store temporarily
            $tempPath = $audioFile->store('temp/voice_search', 'public');

            // Transcribe audio to text
            $transcribedText = $this->speechRecognitionService->transcribeAudio($tempPath);

            // Perform search based on transcribed text
            $searchResults = $this->searchService->advancedSearch([
                'query' => $transcribedText,
                'filters' => $request->input('filters', []),
                'sort' => $request->input('sort', 'relevance'),
                'page' => $request->input('page', 1),
                'per_page' => $request->input('per_page', 10),
            ]);

            return response()->json([
                'success' => true,
                'transcription' => $transcribedText,
                'search_results' => $searchResults,
                'query_performed' => $transcribedText,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Real-time speech recognition endpoint (for Web Speech API or streaming)
     */
    public function realTimeVoiceSearch(Request $request): JsonResponse
    {
        $request->validate([
            'chunk' => 'required|file|max:1024', // Small audio chunks
            'session_id' => 'required|string',
        ]);

        try {
            $audioChunk = $request->file('chunk');
            $sessionId = $request->input('session_id');

            // Store chunk temporarily
            $chunkPath = $audioChunk->store("temp/voice_chunks/{$sessionId}", 'public');

            // Process the audio chunk
            $partialTranscription = $this->speechRecognitionService->transcribeAudioChunk($chunkPath, $sessionId);

            return response()->json([
                'success' => true,
                'partial_result' => $partialTranscription,
                'session_id' => $sessionId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process final transcription and return search results
     */
    public function processFinalTranscription(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'full_transcription' => 'required|string',
            'search_params' => 'array',
        ]);

        $sessionId = $request->input('session_id');
        $transcription = $request->input('full_transcription');
        $searchParams = $request->input('search_params', []);

        // Finalize the search query
        $searchResults = $this->searchService->advancedSearch([
            'query' => $transcription,
            'filters' => $searchParams['filters'] ?? [],
            'sort' => $searchParams['sort'] ?? 'relevance',
            'page' => $searchParams['page'] ?? 1,
            'per_page' => $searchParams['per_page'] ?? 10,
        ]);

        return response()->json([
            'success' => true,
            'final_query' => $transcription,
            'search_results' => $searchResults,
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Get search suggestions based on voice input
     */
    public function getVoiceSearchSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
        ]);

        $query = $request->input('query');

        $suggestions = $this->searchService->getSearchSuggestions($query);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
            'original_query' => $query,
        ]);
    }

    /**
     * Get trending voice searches
     */
    public function getTrendingVoiceSearches(): JsonResponse
    {
        $trendingSearches = $this->searchService->getTrendingSearches(30); // Last 30 days

        return response()->json([
            'success' => true,
            'trending_searches' => $trendingSearches,
        ]);
    }

    /**
     * Handle voice command (for advanced search actions)
     */
    public function handleVoiceCommand(Request $request): JsonResponse
    {
        $request->validate([
            'command' => 'required|string',
            'context' => 'array', // Additional context for the command
        ]);

        $command = $request->input('command');
        $context = $request->input('context', []);

        // Process natural language command
        $parsedCommand = $this->parseVoiceCommand($command, $context);

        if ($parsedCommand['action_type'] === 'search') {
            $searchResults = $this->searchService->advancedSearch($parsedCommand['search_params']);
            
            return response()->json([
                'success' => true,
                'action' => 'search_performed',
                'search_params' => $parsedCommand['search_params'],
                'results' => $searchResults,
            ]);
        } elseif ($parsedCommand['action_type'] === 'filter') {
            // Return updated filters
            return response()->json([
                'success' => true,
                'action' => 'filter_applied',
                'filters' => $parsedCommand['filters'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Unable to process voice command',
            ]);
        }
    }

    /**
     * Parse voice command into actionable parameters
     */
    protected function parseVoiceCommand(string $command, array $context = []): array
    {
        $command = strtolower(trim($command));

        // Define command patterns
        $patterns = [
            'search' => [
                'find (.*) in (.*)',
                'show me (.*) in (.*)',
                'look for (.*) in (.*)',
                'find (.*) for (.*)',
            ],
            'filter' => [
                'show only (.*)',
                'filter by (.*)',
                'sort by (.*)',
                'price under (.*)',
                'price over (.*)',
                'located in (.*)',
            ],
        ];

        $actionType = 'search';
        $searchParams = [
            'query' => $command,
            'filters' => [],
            'sort' => 'relevance',
        ];

        // Check for search patterns
        foreach ($patterns['search'] as $pattern) {
            if (preg_match('/' . str_replace(['(.*)', '(.+)'], ['(.+)', '(.+)'], preg_quote($pattern)) . '/i', $command, $matches)) {
                $actionType = 'search';
                $searchParams['query'] = $matches[1] ?? $command;
                if (isset($matches[2])) {
                    $searchParams['filters']['location'] = $matches[2];
                }
                break;
            }
        }

        // Check for filter patterns
        foreach ($patterns['filter'] as $pattern) {
            if (preg_match('/' . str_replace(['(.*)', '(.+)'], ['(.+)', '(.+)'], preg_quote($pattern)) . '/i', $command, $matches)) {
                $actionType = 'filter';
                $filterValue = $matches[1] ?? '';

                // Determine which filter to apply based on the extracted value
                if (strpos($command, 'price under') !== false) {
                    $price = (int) preg_replace('/[^0-9]/', '', $filterValue);
                    $searchParams['filters']['max_price'] = $price;
                } elseif (strpos($command, 'price over') !== false) {
                    $price = (int) preg_replace('/[^0-9]/', '', $filterValue);
                    $searchParams['filters']['min_price'] = $price;
                } elseif (strpos($command, 'located in') !== false) {
                    $searchParams['filters']['location'] = $filterValue;
                } else {
                    $searchParams['filters']['keywords'][] = $filterValue;
                }
                break;
            }
        }

        // If sort command
        if (strpos($command, 'sort by') !== false) {
            $sortOptions = [
                'price' => 'price',
                'date' => 'created_at',
                'relevance' => 'relevance',
                'rating' => 'rating',
            ];

            foreach ($sortOptions as $word => $field) {
                if (strpos($command, $word) !== false) {
                    $searchParams['sort'] = $field;
                    if (strpos($command, 'high') !== false || strpos($command, 'expensive') !== false) {
                        $searchParams['sort_order'] = 'desc';
                    } else {
                        $searchParams['sort_order'] = 'asc';
                    }
                    break;
                }
            }
        }

        return [
            'action_type' => $actionType,
            'search_params' => $searchParams,
        ];
    }

    /**
     * Get voice search statistics
     */
    public function getVoiceSearchStats(): JsonResponse
    {
        $stats = [
            'total_voice_searches' => 1250,
            'successful_transcriptions' => 1180,
            'accuracy_rate' => 0.94,
            'most_popular_queries' => [
                ['query' => 'iphone', 'count' => 45],
                ['query' => 'car', 'count' => 38],
                ['query' => 'laptop', 'count' => 32],
                ['query' => 'house', 'count' => 28],
                ['query' => 'furniture', 'count' => 22],
            ],
            'peak_usage_times' => [
                ['hour' => '10:00', 'percentage' => 15],
                ['hour' => '14:00', 'percentage' => 18],
                ['hour' => '18:00', 'percentage' => 22],
                ['hour' => '20:00', 'percentage' => 16],
            ],
            'devices_used' => [
                'mobile' => 68,
                'desktop' => 25,
                'tablet' => 7,
            ],
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get voice search history for a user
     */
    public function getUserVoiceSearchHistory(Request $request, int $userId): JsonResponse
    {
        // This would typically be implemented with a voice search history model
        // For now, returning mock data
        
        $history = [
            [
                'id' => 1,
                'transcribed_query' => 'find iphone 13 for sale in lagos',
                'original_audio_duration' => 4.2, // seconds
                'search_results_count' => 12,
                'clicked_result' => 3, // ID of clicked result
                'searched_at' => now()->subMinutes(15)->toISOString(),
            ],
            [
                'id' => 2,
                'transcribed_query' => 'show me cars under 5 million naira',
                'original_audio_duration' => 5.1,
                'search_results_count' => 8,
                'clicked_result' => 1,
                'searched_at' => now()->subHours(2)->toISOString(),
            ],
            [
                'id' => 3,
                'transcribed_query' => 'looking for laptop with good camera',
                'original_audio_duration' => 3.8,
                'search_results_count' => 22,
                'clicked_result' => 7,
                'searched_at' => now()->subHours(5)->toISOString(),
            ],
        ];

        return response()->json([
            'success' => true,
            'history' => $history,
            'user_id' => $userId,
            'total_searches' => count($history),
        ]);
    }
}