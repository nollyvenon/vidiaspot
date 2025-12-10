<?php

namespace App\Http\Controllers;

use App\Services\EducationalContentAboutSustainabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationalContentAboutSustainabilityController extends Controller
{
    private EducationalContentAboutSustainabilityService $sustainabilityService;

    public function __construct()
    {
        $this->sustainabilityService = new EducationalContentAboutSustainabilityService();
    }

    /**
     * Get all sustainability education topics.
     */
    public function getEducationTopics()
    {
        $topics = $this->sustainabilityService->getEducationTopics();

        return response()->json([
            'topics' => $topics,
            'message' => 'Sustainability education topics retrieved successfully'
        ]);
    }

    /**
     * Get a specific education topic.
     */
    public function getEducationTopic(Request $request, string $topicId)
    {
        $topic = $this->sustainabilityService->getEducationTopic($topicId);

        if (!$topic) {
            return response()->json([
                'error' => 'Education topic not found'
            ], 404);
        }

        return response()->json([
            'topic' => $topic,
            'message' => 'Education topic retrieved successfully'
        ]);
    }

    /**
     * Get difficulty levels.
     */
    public function getDifficultyLevels()
    {
        $levels = $this->sustainabilityService->getDifficultyLevels();

        return response()->json([
            'difficulty_levels' => $levels,
            'message' => 'Difficulty levels retrieved successfully'
        ]);
    }

    /**
     * Get content categories.
     */
    public function getContentCategories()
    {
        $categories = $this->sustainabilityService->getContentCategories();

        return response()->json([
            'categories' => $categories,
            'message' => 'Content categories retrieved successfully'
        ]);
    }

    /**
     * Get filtered sustainability content.
     */
    public function getFilteredContent(Request $request)
    {
        $request->validate([
            'category' => 'string',
            'difficulty' => 'string',
            'target_audience' => 'string',
            'search' => 'string',
        ]);

        $filters = $request->only(['category', 'difficulty', 'target_audience', 'search']);

        $userId = Auth::id();
        $content = $this->sustainabilityService->getFilteredContent($filters, $userId);

        return response()->json([
            'content' => $content,
            'filters' => $filters,
            'message' => 'Filtered sustainability content retrieved successfully'
        ]);
    }

    /**
     * Get user progress on sustainability content.
     */
    public function getUserProgress()
    {
        $userId = Auth::id();
        $progress = $this->sustainabilityService->getUserProgress($userId);

        return response()->json([
            'progress' => $progress,
            'message' => 'User progress retrieved successfully'
        ]);
    }

    /**
     * Get a learning pathway recommendation.
     */
    public function getLearningPathway(Request $request)
    {
        $request->validate([
            'focus_area' => 'string',
        ]);

        $userId = Auth::id();
        $pathway = $this->sustainabilityService->getLearningPathway($userId, $request->focus_area);

        return response()->json([
            'pathway' => $pathway,
            'message' => 'Learning pathway recommendation retrieved successfully'
        ]);
    }

    /**
     * Mark a topic as completed.
     */
    public function markTopicCompleted(Request $request, string $topicId)
    {
        $userId = Auth::id();
        
        try {
            $result = $this->sustainabilityService->markTopicCompleted($userId, $topicId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Save a topic for later reading.
     */
    public function saveTopicForLater(Request $request, string $topicId)
    {
        $userId = Auth::id();
        
        try {
            $result = $this->sustainabilityService->saveTopicForLater($userId, $topicId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get sustainability resources and tools.
     */
    public function getSustainabilityResources()
    {
        $resources = $this->sustainabilityService->getSustainabilityResources();

        return response()->json([
            'resources' => $resources,
            'message' => 'Sustainability resources retrieved successfully'
        ]);
    }

    /**
     * Get a sustainability content quiz.
     */
    public function getSustainabilityQuiz(Request $request, string $topicId)
    {
        $quiz = $this->sustainabilityService->getSustainabilityQuiz($topicId);

        if (!$quiz) {
            return response()->json([
                'error' => 'No quiz available for this topic'
            ], 404);
        }

        return response()->json([
            'quiz' => $quiz,
            'message' => 'Sustainability quiz retrieved successfully'
        ]);
    }

    /**
     * Get content by category.
     */
    public function getContentByCategory(Request $request, string $category)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50',
        ]);

        $content = $this->sustainabilityService->getContentByCategory($category, $request->limit ?? 10);

        return response()->json([
            'content' => $content,
            'message' => 'Content by category retrieved successfully'
        ]);
    }

    /**
     * Get user engagement metrics for sustainability content.
     */
    public function getUserEngagementMetrics()
    {
        $userId = Auth::id();
        $metrics = $this->sustainabilityService->getUserEngagementMetrics($userId);

        return response()->json([
            'metrics' => $metrics,
            'message' => 'User engagement metrics retrieved successfully'
        ]);
    }
}