<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class EducationalContentAboutSustainabilityService
{
    /**
     * Available sustainability education topics and content types
     */
    private array $educationTopics = [
        'reducing_carbon_footprint' => [
            'title' => 'Reducing Your Carbon Footprint',
            'description' => 'Understanding how daily choices affect your carbon emissions',
            'category' => 'climate',
            'difficulty' => 'beginner',
            'target_audience' => ['all'],
            'estimated_completion' => '15 minutes',
            'content_types' => ['article', 'infographic', 'quiz'],
            'learning_outcomes' => [
                'Calculate your personal carbon footprint',
                'Identify high-impact lifestyle changes',
                'Understand transportation and diet impacts',
            ],
        ],
        'sustainable_packaging' => [
            'title' => 'Sustainable Packaging Choices',
            'description' => 'Making eco-friendly decisions about packaging',
            'category' => 'waste_reduction',
            'difficulty' => 'intermediate',
            'target_audience' => ['consumers', 'businesses'],
            'estimated_completion' => '20 minutes',
            'content_types' => ['guide', 'comparison_chart', 'interactive_tool'],
            'learning_outcomes' => [
                'Differentiate between packaging materials',
                'Choose sustainable options',
                'Understand recycling symbols',
            ],
        ],
        'energy_efficient_living' => [
            'title' => 'Energy Efficient Living',
            'description' => 'Tips and tools for reducing household energy consumption',
            'category' => 'energy',
            'difficulty' => 'beginner',
            'target_audience' => ['all'],
            'estimated_completion' => '25 minutes',
            'content_types' => ['checklist', 'video', 'savings_calculator'],
            'learning_outcomes' => [
                'Implement energy-saving practices',
                'Calculate potential savings',
                'Identify energy-efficient appliances',
            ],
        ],
        'sustainable_shopping' => [
            'title' => 'Sustainable Shopping Habits',
            'description' => 'How to shop in ways that support environmental sustainability',
            'category' => 'consumption',
            'difficulty' => 'beginner',
            'target_audience' => ['all'],
            'estimated_completion' => '18 minutes',
            'content_types' => ['article', 'buying_guide', 'decision_tree'],
            'learning_outcomes' => [
                'Recognize sustainable product certifications',
                'Make informed purchasing decisions',
                'Understand lifecycle impacts',
            ],
        ],
        'waste_reduction_strategies' => [
            'title' => 'Waste Reduction Strategies',
            'description' => 'Practical approaches to minimize waste generation',
            'category' => 'waste_reduction',
            'difficulty' => 'beginner',
            'target_audience' => ['all'],
            'estimated_completion' => '22 minutes',
            'content_types' => ['infographic', 'step_by_step_guide', 'challenge_setup'],
            'learning_outcomes' => [
                'Apply the 5 R\'s (Refuse, Reduce, Reuse, Repair, Recycle)',
                'Create effective waste reduction habits',
                'Implement composting systems',
            ],
        ],
        'water_conservation_tips' => [
            'title' => 'Water Conservation Tips',
            'description' => 'Efficient water use in households and communities',
            'category' => 'resources',
            'difficulty' => 'beginner',
            'target_audience' => ['all'],
            'estimated_completion' => '12 minutes',
            'content_types' => ['tipsheet', 'water_audit_tool', 'infographic'],
            'learning_outcomes' => [
                'Implement household water conservation',
                'Calculate water savings',
                'Identify leak prevention methods',
            ],
        ],
        'circular_economy_principles' => [
            'title' => 'Circular Economy Principles',
            'description' => 'Understanding systems that eliminate waste and promote continuous use',
            'category' => 'economics',
            'difficulty' => 'intermediate',
            'target_audience' => ['businesses', 'policymakers'],
            'estimated_completion' => '35 minutes',
            'content_types' => ['video_series', 'case_studies', 'framework_toolkit'],
            'learning_outcomes' => [
                'Apply circular economy concepts',
                'Design product lifecycles',
                'Identify circular business models',
            ],
        ],
        'local_food_systems' => [
            'title' => 'Local Food Systems',
            'description' => 'Benefits of local food production and consumption',
            'category' => 'food',
            'difficulty' => 'beginner',
            'target_audience' => ['all'],
            'estimated_completion' => '28 minutes',
            'content_types' => ['article', 'seasonal_eating_guide', 'farmers_market_locator'],
            'learning_outcomes' => [
                'Identify local foods in your area',
                'Understand food system impacts',
                'Plan seasonal eating',
            ],
        ],
    ];

    /**
     * Content difficulty levels
     */
    private array $difficultyLevels = [
        'beginner' => [
            'name' => 'Beginner',
            'description' => 'No prior knowledge required',
            'icon' => '🌱',
        ],
        'intermediate' => [
            'name' => 'Intermediate',
            'description' => 'Some background knowledge helpful',
            'icon' => '🌿',
        ],
        'advanced' => [
            'name' => 'Advanced',
            'description' => 'In-depth expertise or study required',
            'icon' => '🌳',
        ],
    ];

    /**
     * Content categories
     */
    private array $contentCategories = [
        'climate' => [
            'name' => 'Climate Change',
            'description' => 'Content about greenhouse gases, emissions, and climate impacts',
            'icon' => '🌡️',
        ],
        'waste_reduction' => [
            'name' => 'Waste Reduction',
            'description' => 'Content about reducing, reusing, recycling, and composting',
            'icon' => '♻️',
        ],
        'energy' => [
            'name' => 'Energy',
            'description' => 'Content about energy use, conservation, and renewable sources',
            'icon' => '⚡',
        ],
        'consumption' => [
            'name' => 'Sustainable Consumption',
            'description' => 'Content about making sustainable purchasing and consumption choices',
            'icon' => '🛒',
        ],
        'resources' => [
            'name' => 'Natural Resources',
            'description' => 'Content about water, land use, and resource conservation',
            'icon' => '💧',
        ],
        'economics' => [
            'name' => 'Sustainable Economics',
            'description' => 'Content about circular economy, green business, and sustainability finance',
            'icon' => '🌍',
        ],
        'food' => [
            'name' => 'Food Systems',
            'description' => 'Content about sustainable agriculture, local food, and food waste',
            'icon' => '🍎',
        ],
    ];

    /**
     * Get all sustainability education topics
     */
    public function getEducationTopics(): array
    {
        return $this->educationTopics;
    }

    /**
     * Get a specific education topic
     */
    public function getEducationTopic(string $topicId): ?array
    {
        return $this->educationTopics[$topicId] ?? null;
    }

    /**
     * Get difficulty levels
     */
    public function getDifficultyLevels(): array
    {
        return $this->difficultyLevels;
    }

    /**
     * Get content categories
     */
    public function getContentCategories(): array
    {
        return $this->contentCategories;
    }

    /**
     * Get filtered and curated content based on user preferences
     */
    public function getFilteredContent(array $filters = [], string $userId = null): array
    {
        $filteredTopics = $this->educationTopics;
        
        // Apply filters based on criteria
        if (!empty($filters['category'])) {
            $filteredTopics = array_filter($filteredTopics, function($topic) use ($filters) {
                return $topic['category'] === $filters['category'];
            });
        }
        
        if (!empty($filters['difficulty'])) {
            $filteredTopics = array_filter($filteredTopics, function($topic) use ($filters) {
                return $topic['difficulty'] === $filters['difficulty'];
            });
        }
        
        if (!empty($filters['target_audience'])) {
            $filteredTopics = array_filter($filteredTopics, function($topic) use ($filters) {
                return in_array('all', $topic['target_audience']) || 
                       in_array($filters['target_audience'], $topic['target_audience']);
            });
        }
        
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $filteredTopics = array_filter($filteredTopics, function($topic) use ($search) {
                return strpos(strtolower($topic['title']), $search) !== false || 
                       strpos(strtolower($topic['description']), $search) !== false;
            });
        }

        // If user is specified, add their progress and engagement data
        $result = [
            'topics' => array_values($filteredTopics),
            'total_count' => count($filteredTopics),
            'filters_applied' => $filters,
        ];

        if ($userId) {
            $result['user_progress'] = $this->getUserProgress($userId);
        }

        return $result;
    }

    /**
     * Get user's progress on educational content
     */
    public function getUserProgress(string $userId): array
    {
        // Get user's completed topics
        $completedKey = "user_sustainability_topics_completed_{$userId}";
        $completedTopics = \Cache::get($completedKey, []);
        
        // Get user's saved topics for later
        $savedKey = "user_sustainability_topics_saved_{$userId}";
        $savedTopics = \Cache::get($savedKey, []);

        // Calculate overall progress
        $overallProgress = count($completedTopics) > 0 ? 
                          min(100, intval((count($completedTopics) / count($this->educationTopics)) * 100)) : 0;

        return [
            'user_id' => $userId,
            'completed_topics' => count($completedTopics),
            'saved_topics' => count($savedTopics),
            'overall_progress' => $overallProgress,
            'completed_topic_ids' => $completedTopics,
            'saved_topic_ids' => $savedTopics,
            'recommended_next' => $this->getRecommendedNextSteps($completedTopics, $userId),
        ];
    }

    /**
     * Get recommended next steps based on completed topics
     */
    private function getRecommendedNextSteps(array $completedTopics, string $userId): array
    {
        $allTopicIds = array_keys($this->educationTopics);
        
        // Find topics not yet completed
        $remainingTopics = array_diff($allTopicIds, $completedTopics);
        $remainingTopics = array_values($remainingTopics);
        
        // Randomly select some recommendations (in a real implementation, this would be more sophisticated)
        shuffle($remainingTopics);
        $recommendations = array_slice($remainingTopics, 0, min(5, count($remainingTopics)));

        $formattedRecommendations = [];
        foreach ($recommendations as $topicId) {
            $formattedRecommendations[] = [
                'id' => $topicId,
                'title' => $this->educationTopics[$topicId]['title'],
                'category' => $this->educationTopics[$topicId]['category'],
                'estimated_time' => $this->educationTopics[$topicId]['estimated_completion'],
            ];
        }

        return $formattedRecommendations;
    }

    /**
     * Get learning pathway recommendations
     */
    public function getLearningPathway(string $userId, string $focusArea = null): array
    {
        // Define pathway recommendations based on user progress and focus area
        $completed = \Cache::get("user_sustainability_topics_completed_{$userId}", []);
        
        // Default pathways
        $pathways = [
            'beginner' => [
                'title' => 'Sustainability Journey Starter',
                'description' => 'A beginner-friendly introduction to sustainability concepts',
                'topics_sequence' => [
                    'reducing_carbon_footprint',
                    'waste_reduction_strategies', 
                    'sustainable_shopping',
                    'water_conservation_tips'
                ],
                'estimated_duration' => '1.5 hours',
            ],
            'intermediate' => [
                'title' => 'Sustainability Deep Dive',
                'description' => 'Intermediate exploration of specific sustainability topics',
                'topics_sequence' => [
                    'sustainable_packaging',
                    'energy_efficient_living',
                    'local_food_systems',
                    'circular_economy_principles'
                ],
                'estimated_duration' => '2.5 hours',
            ],
            'focus_climate' => [
                'title' => 'Climate Action Specialist',
                'description' => 'Focused learning path on climate change and carbon reduction',
                'topics_sequence' => [
                    'reducing_carbon_footprint',
                    'energy_efficient_living',
                    'sustainable_shopping',
                    'circular_economy_principles'
                ],
                'estimated_duration' => '2.0 hours',
            ],
        ];

        // Determine appropriate pathway based on user progress and preferences
        $completedBeginner = count(array_intersect(['reducing_carbon_footprint', 'waste_reduction_strategies'], $completed));
        $totalTopics = count($this->educationTopics);
        $completionPercentage = count($completed) / $totalTopics * 100;

        if ($focusArea) {
            $pathwayKey = "focus_{$focusArea}";
            $pathway = $pathways[$pathwayKey] ?? null;
        } elseif ($completionPercentage < 20) {
            $pathway = $pathways['beginner'];
        } elseif ($completionPercentage < 50) {
            $pathway = $pathways['intermediate'];
        } else {
            $pathway = $pathways['intermediate']; // Advanced learners can choose any path
        }

        // Filter pathway topics to only include those not completed
        $uncompletedTopics = [];
        foreach ($pathway['topics_sequence'] as $topicId) {
            if (!in_array($topicId, $completed)) {
                $uncompletedTopics[] = $topicId;
            }
        }

        // Update pathway with only uncompleted topics
        $pathway['topics_sequence'] = $uncompletedTopics;

        return [
            'pathway' => $pathway,
            'user_id' => $userId,
            'completed_topics' => count($completed),
            'total_topics' => $totalTopics,
            'completion_percentage' => round($completionPercentage, 2),
            'suggested_pathway' => $pathway['title'],
        ];
    }

    /**
     * Mark a topic as completed by the user
     */
    public function markTopicCompleted(string $userId, string $topicId): array
    {
        $topic = $this->getEducationTopic($topicId);
        if (!$topic) {
            throw new \InvalidArgumentException("Topic not found: {$topicId}");
        }

        $completedKey = "user_sustainability_topics_completed_{$userId}";
        $completed = \Cache::get($completedKey, []);
        
        if (!in_array($topicId, $completed)) {
            $completed[] = $topicId;
            \Cache::put($completedKey, $completed, now()->addYear());
        }

        // Update user's progress
        $userProgress = $this->getUserProgress($userId);

        // Calculate achievement if applicable
        $achievement = $this->evaluateAchievement($userId, $userProgress);

        return [
            'success' => true,
            'topic_id' => $topicId,
            'topic_title' => $topic['title'],
            'user_progress' => $userProgress,
            'achievement_unlocked' => $achievement,
            'message' => "Topic '{$topic['title']}' marked as completed",
        ];
    }

    /**
     * Save a topic for later reading
     */
    public function saveTopicForLater(string $userId, string $topicId): array
    {
        $topic = $this->getEducationTopic($topicId);
        if (!$topic) {
            throw new \InvalidArgumentException("Topic not found: {$topicId}");
        }

        $savedKey = "user_sustainability_topics_saved_{$userId}";
        $saved = \Cache::get($savedKey, []);
        
        if (!in_array($topicId, $saved)) {
            $saved[] = $topicId;
            \Cache::put($savedKey, $saved, now()->addYear());
        }

        return [
            'success' => true,
            'topic_id' => $topicId,
            'topic_title' => $topic['title'],
            'message' => "Topic '{$topic['title']}' saved for later",
        ];
    }

    /**
     * Evaluate if user earned any achievements
     */
    private function evaluateAchievement(string $userId, array $userProgress): ?array
    {
        $achievements = [];
        $completedCount = $userProgress['completed_topics'];

        // Check for various achievements
        if ($completedCount >= 1) {
            $achievements[] = [
                'id' => 'first_step',
                'name' => 'First Step',
                'description' => 'Complete your first sustainability topic',
                'badge' => '🌱',
            ];
        }

        if ($completedCount >= 5) {
            $achievements[] = [
                'id' => 'learner',
                'name' => 'Sustainability Learner',
                'description' => 'Complete 5 sustainability topics',
                'badge' => '🌿',
            ];
        }

        if ($completedCount >= 10) {
            $achievements[] = [
                'id' => 'explorer',
                'name' => 'Sustainability Explorer',
                'description' => 'Complete 10 sustainability topics',
                'badge' => '🌳',
            ];
        }

        if ($userProgress['overall_progress'] >= 50) {
            $achievements[] = [
                'id' => 'champion',
                'name' => 'Sustainability Champion',
                'description' => 'Complete 50% of available sustainability topics',
                'badge' => '🏆',
            ];
        }

        if ($userProgress['overall_progress'] >= 100) {
            $achievements[] = [
                'id' => 'master',
                'name' => 'Sustainability Master',
                'description' => 'Complete all available sustainability topics',
                'badge' => '👑',
            ];
        }

        // Return the highest achievement earned
        if (!empty($achievements)) {
            // In a real implementation, we might track which achievements have been awarded
            return end($achievements);
        }

        return null;
    }

    /**
     * Get sustainability resources and tools
     */
    public function getSustainabilityResources(): array
    {
        $resources = [
            [
                'id' => 'carbon_calculator',
                'title' => 'Personal Carbon Footprint Calculator',
                'description' => 'Calculate your annual carbon emissions',
                'type' => 'tool',
                'category' => 'climate',
                'url' => '/sustainability-tools/carbon-calculator',
                'estimated_time' => '5-10 minutes',
                'difficulty' => 'beginner',
            ],
            [
                'id' => 'packaging_guide',
                'title' => 'Sustainable Packaging Guide',
                'description' => 'Quick reference for eco-friendly packaging choices',
                'type' => 'guide',
                'category' => 'waste_reduction',
                'url' => '/sustainability-tools/packaging-guide',
                'estimated_time' => '2-5 minutes',
                'difficulty' => 'beginner',
            ],
            [
                'id' => 'water_auditor',
                'title' => 'Home Water Audit Tool',
                'description' => 'Assess and improve your home water efficiency',
                'type' => 'tool',
                'category' => 'resources',
                'url' => '/sustainability-tools/water-auditor',
                'estimated_time' => '10-15 minutes',
                'difficulty' => 'beginner',
            ],
            [
                'id' => 'energy_savings',
                'title' => 'Energy Savings Calculator',
                'description' => 'Estimate potential energy cost savings',
                'type' => 'tool',
                'category' => 'energy',
                'url' => '/sustainability-tools/energy-savings',
                'estimated_time' => '5-10 minutes',
                'difficulty' => 'beginner',
            ],
            [
                'id' => 'shopping_decisions',
                'title' => 'Sustainable Shopping Decision Tree',
                'description' => 'Guided decision-making for eco-friendly purchases',
                'type' => 'tool',
                'category' => 'consumption',
                'url' => '/sustainability-tools/shopping-decision-tree',
                'estimated_time' => '3-5 minutes',
                'difficulty' => 'intermediate',
            ],
        ];

        return [
            'resources' => $resources,
            'total_count' => count($resources),
            'categories' => array_keys($this->contentCategories),
        ];
    }

    /**
     * Get sustainability content quiz
     */
    public function getSustainabilityQuiz(string $topicId): ?array
    {
        $topic = $this->getEducationTopic($topicId);
        if (!$topic) {
            return null;
        }

        $quizzes = [
            'reducing_carbon_footprint' => [
                'id' => 'carbon-quiz-' . Str::random(6),
                'topic_id' => $topicId,
                'title' => 'Carbon Footprint Quiz',
                'questions' => [
                    [
                        'id' => 1,
                        'question' => 'Which transportation method typically has the lowest carbon footprint per passenger?',
                        'options' => ['Driving alone', 'Flying', 'Taking the bus', 'Motorcycle'],
                        'correct_answer' => 'Taking the bus',
                        'explanation' => 'Public transportation is more efficient per passenger than private vehicles.',
                    ],
                    [
                        'id' => 2,
                        'question' => 'What is the approximate carbon footprint of a typical beef meal compared to a vegetarian meal?',
                        'options' => ['Same', '2 times higher', '4 times higher', '10 times higher'],
                        'correct_answer' => '4 times higher',
                        'explanation' => 'Beef production generates significantly more greenhouse gases than plant-based alternatives.',
                    ],
                    [
                        'id' => 3,
                        'question' => 'Which of these has the biggest impact on your carbon footprint?',
                        'options' => ['Light bulbs', 'Diet choices', 'Shower time', 'Recycling'],
                        'correct_answer' => 'Diet choices',
                        'explanation' => 'Food choices, particularly animal products, significantly impact your carbon footprint.',
                    ],
                ],
            ],
            'sustainable_shopping' => [
                'id' => 'shopping-quiz-' . Str::random(6),
                'topic_id' => $topicId,
                'title' => 'Sustainable Shopping Quiz',
                'questions' => [
                    [
                        'id' => 1,
                        'question' => 'What does the "B Corp" certification primarily focus on?',
                        'options' => ['Environmental impact', 'Social responsibility', 'Both environment and society', 'Profit maximization'],
                        'correct_answer' => 'Both environment and society',
                        'explanation' => 'B Corp certification evaluates a company\'s impact on all stakeholders - workers, customers, suppliers, community, and environment.',
                    ],
                    [
                        'id' => 2,
                        'question' => 'Which approach typically has the smallest environmental footprint?',
                        'options' => ['Buying new', 'Buying local', 'Buying used', 'Buying organic'],
                        'correct_answer' => 'Buying used',
                        'explanation' => 'Extending the life of existing products by purchasing used items avoids the environmental impact of producing new ones.',
                    ],
                ],
            ],
        ];

        return $quizzes[$topicId] ?? null;
    }

    /**
     * Get sustainability content by category
     */
    public function getContentByCategory(string $category, int $limit = 10): array
    {
        $categoryContent = array_filter($this->educationTopics, function($topic) use ($category) {
            return $topic['category'] === $category;
        });

        $sortedContent = array_slice($categoryContent, 0, $limit);

        return [
            'category' => $category,
            'content' => array_values($sortedContent),
            'total_in_category' => count($categoryContent),
            'returned_count' => count($sortedContent),
            'category_info' => $this->contentCategories[$category] ?? null,
        ];
    }

    /**
     * Get user engagement metrics for sustainability content
     */
    public function getUserEngagementMetrics(string $userId): array
    {
        $progress = $this->getUserProgress($userId);
        $completed = $progress['completed_topic_ids'];

        $timeSpent = 0;
        foreach ($completed as $topicId) {
            if (isset($this->educationTopics[$topicId])) {
                // Parse time (e.g., "15 minutes") to add to total
                $timeStr = $this->educationTopics[$topicId]['estimated_completion'];
                if (preg_match('/(\d+)\s+minutes?/', $timeStr, $matches)) {
                    $timeSpent += intval($matches[1]);
                }
            }
        }

        return [
            'user_id' => $userId,
            'total_topics_completed' => count($completed),
            'estimated_learning_time_minutes' => $timeSpent,
            'overall_completion_percentage' => $progress['overall_progress'],
            'topics_by_difficulty' => $this->getTopicsByDifficulty($completed),
            'topics_by_category' => $this->getTopicsByCategory($completed),
            'last_accessed' => now()->toISOString(), // Would be tracked in real implementation
        ];
    }

    /**
     * Get topics grouped by difficulty
     */
    private function getTopicsByDifficulty(array $completedTopics): array
    {
        $byDifficulty = [];
        foreach ($completedTopics as $topicId) {
            if (isset($this->educationTopics[$topicId])) {
                $difficulty = $this->educationTopics[$topicId]['difficulty'];
                if (!isset($byDifficulty[$difficulty])) {
                    $byDifficulty[$difficulty] = 0;
                }
                $byDifficulty[$difficulty]++;
            }
        }
        return $byDifficulty;
    }

    /**
     * Get topics grouped by category
     */
    private function getTopicsByCategory(array $completedTopics): array
    {
        $byCategory = [];
        foreach ($completedTopics as $topicId) {
            if (isset($this->educationTopics[$topicId])) {
                $category = $this->educationTopics[$topicId]['category'];
                if (!isset($byCategory[$category])) {
                    $byCategory[$category] = 0;
                }
                $byCategory[$category]++;
            }
        }
        return $byCategory;
    }
}