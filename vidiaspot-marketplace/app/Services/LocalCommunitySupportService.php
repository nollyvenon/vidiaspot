<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class LocalCommunitySupportService
{
    /**
     * Types of community support initiatives
     */
    private array $supportInitiatives = [
        'local_business_spotlight' => [
            'name' => 'Local Business Spotlight',
            'description' => 'Promote and highlight local businesses',
            'criteria' => ['location_within_radius', 'business_size_requirement', 'community_impact'],
            'frequency' => 'weekly',
            'reward_type' => 'visibility',
            'benefits' => ['promotional_featured', 'reduced_fee', 'marketing_support'],
        ],
        'neighborhood_events' => [
            'name' => 'Neighborhood Events',
            'description' => 'Organize and promote community events',
            'criteria' => ['event_community_focus', 'volunteer_participation', 'local_attendance_target'],
            'frequency' => 'monthly',
            'reward_type' => 'participation',
            'benefits' => ['venue_discount', 'marketing_support', 'community_award'],
        ],
        'community_volunteers' => [
            'name' => 'Community Volunteers',
            'description' => 'Connect volunteers with local opportunities',
            'criteria' => ['availability', 'skills_matching', 'commitment_duration'],
            'frequency' => 'ongoing',
            'reward_type' => 'recognition',
            'benefits' => ['community_points', 'discount_codes', 'certification'],
        ],
        'local_resource_sharing' => [
            'name' => 'Resource Sharing Network',
            'description' => 'Facilitate sharing of resources among neighbors',
            'criteria' => ['item_condition', 'sharing_duration', 'safety_compliance'],
            'frequency' => 'as_needed',
            'reward_type' => 'community_connection',
            'benefits' => ['fee_reduction', 'trusted_member_badge', 'resource_pools'],
        ],
        'local_job_board' => [
            'name' => 'Local Job Board',
            'description' => 'Connect local employers with workers',
            'criteria' => ['job_locality', 'fair_wage', 'work_conditions'],
            'frequency' => 'ongoing',
            'reward_type' => 'connection',
            'benefits' => ['job_promotion', 'candidate_matching', 'success_bonus'],
        ],
        'skills_exchange' => [
            'name' => 'Skills Exchange Program',
            'description' => 'Community members teaching skills to each other',
            'criteria' => ['expertise_verification', 'teaching_commitment', 'community_interest'],
            'frequency' => 'ongoing',
            'reward_type' => 'mutual_benefit',
            'benefits' => ['skill_credit_system', 'recognition', 'networking'],
        ],
    ];

    /**
     * Community engagement metrics
     */
    private array $engagementMetrics = [
        'participation_rate',
        'local_purchase_percentage',
        'community_event_attendance',
        'local_business_interaction',
        'neighborhood_connection',
        'volunteer_hours_contributed',
    ];

    /**
     * Get available community support initiatives
     */
    public function getSupportInitiatives(): array
    {
        return $this->supportInitiatives;
    }

    /**
     * Get local businesses near a user's location
     */
    public function getLocalBusinesses(array $location, int $radiusMiles = 10): array
    {
        // In a real implementation, this would query a database of local businesses
        // For this implementation, we'll return sample data based on location
        
        $sampleBusinesses = [
            [
                'id' => 'biz-' . Str::random(8),
                'name' => 'Johnson\'s Bakery',
                'category' => 'food',
                'distance_miles' => mt_rand(1, 8),
                'rating' => round(mt_rand(70, 100) / 10, 1),
                'delivery_available' => true,
                'is_local_owner' => true,
                'certifications' => ['locally_owned', 'community_partner'],
                'description' => 'Family-owned bakery serving fresh goods since 1995',
                'address' => $this->generateFakeAddress($location),
                'contact' => [
                    'phone' => '+1-' . mt_rand(100, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(1000, 9999),
                    'email' => 'info@johnsons-bakery.local',
                ],
                'hours' => [
                    'mon_fri' => '7 AM - 7 PM',
                    'sat' => '8 AM - 6 PM',
                    'sun' => '10 AM - 4 PM',
                ],
                'social_impact_score' => mt_rand(70, 95),
            ],
            [
                'id' => 'biz-' . Str::random(8),
                'name' => 'Eco Cleaner Services',
                'category' => 'services',
                'distance_miles' => mt_rand(1, 12),
                'rating' => round(mt_rand(75, 100) / 10, 1),
                'delivery_available' => false,
                'is_local_owner' => true,
                'certifications' => ['ecofriendly', 'locally_owned', 'community_partner'],
                'description' => 'Eco-friendly cleaning services supporting local employment',
                'address' => $this->generateFakeAddress($location),
                'contact' => [
                    'phone' => '+1-' . mt_rand(100, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(1000, 9999),
                    'email' => 'service@ecocleaner.local',
                ],
                'hours' => [
                    'mon_sat' => '8 AM - 6 PM',
                    'sun' => 'closed',
                ],
                'social_impact_score' => mt_rand(80, 98),
            ],
            [
                'id' => 'biz-' . Str::random(8),
                'name' => 'Thompson\'s Hardware',
                'category' => 'retail',
                'distance_miles' => mt_rand(2, 15),
                'rating' => round(mt_rand(65, 95) / 10, 1),
                'delivery_available' => true,
                'is_local_owner' => true,
                'certifications' => ['locally_owned', 'family_business'],
                'description' => 'Locally owned hardware store with expert advice',
                'address' => $this->generateFakeAddress($location),
                'contact' => [
                    'phone' => '+1-' . mt_rand(100, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(1000, 9999),
                    'email' => 'info@thompsonshardware.local',
                ],
                'hours' => [
                    'mon_fri' => '8 AM - 7 PM',
                    'sat' => '8 AM - 5 PM',
                    'sun' => '10 AM - 4 PM',
                ],
                'social_impact_score' => mt_rand(75, 90),
            ],
        ];

        // Filter businesses within the specified radius
        $localBusinesses = array_filter($sampleBusinesses, function($business) use ($radiusMiles) {
            return $business['distance_miles'] <= $radiusMiles;
        });

        return [
            'businesses' => array_values($localBusinesses),
            'total_count' => count($localBusinesses),
            'search_radius' => $radiusMiles,
            'location' => $location,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Generate fake address based on location
     */
    private function generateFakeAddress(array $location): array
    {
        $streetNames = [
            'Main Street', 'Oak Avenue', 'Pine Road', 'Elm Street', 'Maple Drive',
            'Cedar Lane', 'Washington Blvd', 'Park Place', 'River Road', 'Hill Street'
        ];
        
        return [
            'street' => mt_rand(100, 9999) . ' ' . $streetNames[array_rand($streetNames)],
            'city' => $location['city'] ?? 'Local City',
            'state' => $location['state'] ?? 'ST',
            'zip' => mt_rand(10000, 99999),
        ];
    }

    /**
     * Get upcoming community events
     */
    public function getUpcomingCommunityEvents(array $location, int $days = 30): array
    {
        $eventTypes = [
            'farmer\'s market',
            'community garage sale',
            'local business fair',
            'environmental cleanup',
            'neighborhood watch meeting',
            'skills workshop',
            'book club',
            'exercise group',
            'childcare co-op',
            'tool lending library',
        ];

        $events = [];
        
        for ($i = 0; $i < 8; $i++) {
            $daysFromNow = mt_rand(1, $days);
            $eventType = $eventTypes[array_rand($eventTypes)];
            
            $events[] = [
                'id' => 'event-' . Str::random(8),
                'name' => ucfirst($eventType) . ' in ' . ($location['city'] ?? 'your area'),
                'type' => $eventType,
                'date' => now()->addDays($daysFromNow)->toISOString(),
                'start_time' => date('H:i', mktime(mt_rand(8, 18), 0, 0)),
                'location' => [
                    'name' => 'Community Center',
                    'address' => $this->generateFakeAddress($location),
                ],
                'organizer' => [
                    'name' => 'Local Community Group',
                    'contact' => '+1-' . mt_rand(100, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(1000, 9999),
                ],
                'attendees_expected' => mt_rand(10, 100),
                'description' => "Join your neighbors for {$eventType}. A great opportunity to connect with your local community and support local initiatives.",
                'registration_required' => in_array($eventType, ['workshop', 'book club']),
                'cost' => $eventType === 'farmer\'s market' ? 'Free' : '$' . mt_rand(0, 15),
                'community_impact_score' => mt_rand(70, 95),
            ];
        }

        // Sort by date
        usort($events, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return [
            'events' => $events,
            'total_count' => count($events),
            'upcoming_days' => $days,
            'location' => $location,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Register user for community engagement
     */
    public function registerForCommunityEngagement(string $userId, array $preferences): array
    {
        // Validate user exists
        $user = \Cache::get("user_{$userId}");
        if (!$user) {
            throw new \InvalidArgumentException("User not found: {$userId}");
        }

        // Store community preferences
        $communityPrefKey = "community_preferences_{$userId}";
        \Cache::put($communityPrefKey, $preferences, now()->addMonths(12));

        // Add user to community engagement tracking
        $communityUserKey = "community_users_{$userId}";
        \Cache::put($communityUserKey, [
            'id' => $userId,
            'joined_at' => now()->toISOString(),
            'preferences' => $preferences,
            'engagement_score' => 0,
            'activities_completed' => 0,
            'local_business_interactions' => 0,
        ], now()->addMonths(12));

        return [
            'success' => true,
            'user_id' => $userId,
            'preferences' => $preferences,
            'message' => 'Successfully registered for community engagement programs',
            'community_benefits' => [
                'exclusive_access' => true,
                'local_discounts' => true,
                'event_invitations' => true,
                'community_badge' => 'Community Member',
            ],
        ];
    }

    /**
     * Get community engagement score for a user
     */
    public function getCommunityEngagementScore(string $userId): array
    {
        $communityUserKey = "community_users_{$userId}";
        $user = \Cache::get($communityUserKey);

        if (!$user) {
            return [
                'user_id' => $userId,
                'engagement_score' => 0,
                'level' => 'Not Engaged',
                'progress' => 0,
                'badges' => [],
                'next_milestone' => 'Complete your first local interaction',
                'message' => 'Join the community engagement program to start earning points!',
            ];
        }

        // Calculate engagement score based on activities
        $score = $user['engagement_score'] ?? 0;
        $activities = $user['activities_completed'] ?? 0;
        $localInteractions = $user['local_business_interactions'] ?? 0;

        // Level calculation
        $level = 'Beginner';
        if ($score >= 50) $level = 'Active Participant';
        if ($score >= 100) $level = 'Community Leader';
        if ($score >= 200) $level = 'Community Champion';

        // Badges earned
        $badges = [];
        if ($activities >= 5) $badges[] = 'First 5 Activities';
        if ($localInteractions >= 10) $badges[] = 'Local Shopper';
        if ($activities >= 20) $badges[] = 'Community Builder';
        if ($localInteractions >= 30) $badges[] = 'Local Supporter';

        // Next milestone
        $nextMilestone = 'Continue participating in community activities';
        if ($localInteractions < 10) $nextMilestone = 'Interact with 10 local businesses';
        if ($activities < 5) $nextMilestone = 'Complete 5 community activities';

        return [
            'user_id' => $userId,
            'engagement_score' => $score,
            'level' => $level,
            'progress' => min(100, intval(($score / 200) * 100)), // 200 points = 100% progress to champion
            'badges' => $badges,
            'activities_completed' => $activities,
            'local_business_interactions' => $localInteractions,
            'next_milestone' => $nextMilestone,
            'message' => 'Keep up the great community engagement!',
        ];
    }

    /**
     * Get local impact recommendations for a user
     */
    public function getLocalImpactRecommendations(string $userId, array $location): array
    {
        $recommendations = [
            [
                'id' => 'rec-1',
                'title' => 'Shop Local',
                'description' => 'Find and support businesses within ' . mt_rand(3, 8) . ' miles of your location',
                'action' => 'Browse local businesses',
                'potential_impact' => mt_rand(15, 25) . ' points',
                'difficulty' => 'easy',
            ],
            [
                'id' => 'rec-2',
                'title' => 'Attend Community Event',
                'description' => 'Join upcoming events in your neighborhood',
                'action' => 'View events calendar',
                'potential_impact' => mt_rand(20, 30) . ' points',
                'difficulty' => 'medium',
            ],
            [
                'id' => 'rec-3',
                'title' => 'Volunteer Opportunity',
                'description' => 'Help with local community initiatives',
                'action' => 'Browse volunteer opportunities',
                'potential_impact' => mt_rand(25, 40) . ' points',
                'difficulty' => 'medium',
            ],
            [
                'id' => 'rec-4',
                'title' => 'Share Resources',
                'description' => 'Join our neighbor-to-neighbor resource sharing',
                'action' => 'Join network',
                'potential_impact' => mt_rand(10, 20) . ' points',
                'difficulty' => 'easy',
            ],
            [
                'id' => 'rec-5',
                'title' => 'Skill Sharing',
                'description' => 'Teach a skill or learn from neighbors',
                'action' => 'Explore skills exchange',
                'potential_impact' => mt_rand(30, 50) . ' points',
                'difficulty' => 'hard',
            ],
        ];

        return [
            'recommendations' => $recommendations,
            'location' => $location,
            'user_id' => $userId,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Record community activity for a user
     */
    public function recordCommunityActivity(string $userId, string $activityType, array $details = []): array
    {
        // Validate user
        $communityUserKey = "community_users_{$userId}";
        $user = \Cache::get($communityUserKey);
        
        if (!$user) {
            throw new \InvalidArgumentException("User not registered for community engagement: {$userId}");
        }

        // Define point values for different activities
        $activityPoints = [
            'local_purchase' => 10,
            'local_business_review' => 5,
            'community_event_attendance' => 15,
            'volunteer_work' => 25,
            'resource_sharing' => 20,
            'skills_teaching' => 30,
            'neighbor_help' => 20,
            'local_job_posting' => 10,
            'community_feedback' => 3,
        ];

        $pointsEarned = $activityPoints[$activityType] ?? 5; // Default to 5 points

        // Update user's engagement stats
        $user['engagement_score'] = ($user['engagement_score'] ?? 0) + $pointsEarned;
        $user['activities_completed'] = ($user['activities_completed'] ?? 0) + 1;
        
        if (in_array($activityType, ['local_purchase', 'local_business_review'])) {
            $user['local_business_interactions'] = ($user['local_business_interactions'] ?? 0) + 1;
        }

        // Save updated user data
        \Cache::put($communityUserKey, $user, now()->addMonths(12));

        // Generate activity record
        $activityId = 'act-' . Str::uuid();
        $activity = [
            'id' => $activityId,
            'user_id' => $userId,
            'activity_type' => $activityType,
            'points_earned' => $pointsEarned,
            'details' => $details,
            'date' => now()->toISOString(),
        ];

        // Store activity
        $activityKey = "community_activity_{$activityId}";
        \Cache::put($activityKey, $activity, now()->addMonths(12));

        // Add to user's activity history
        $userActivitiesKey = "user_community_activities_{$userId}";
        $activities = \Cache::get($userActivitiesKey, []);
        $activities[] = $activityId;
        \Cache::put($userActivitiesKey, $activities, now()->addMonths(12));

        return [
            'success' => true,
            'activity' => $activity,
            'updated_engagement_score' => $user['engagement_score'],
            'message' => "Community activity recorded. Earned {$pointsEarned} points!",
        ];
    }

    /**
     * Get community challenges/competitions
     */
    public function getCommunityChallenges(string $location): array
    {
        $challenges = [
            [
                'id' => 'challenge-' . Str::random(6),
                'title' => 'Local Spending Challenge',
                'description' => 'Spend 50% of your purchases locally in the next 30 days',
                'goal' => '50% local spending',
                'duration_days' => 30,
                'participants' => mt_rand(50, 200),
                'prize' => 'Gift cards to local businesses',
                'progress' => mt_rand(30, 80) . '% of participants on track',
                'started_at' => now()->subDays(mt_rand(1, 10))->toISOString(),
            ],
            [
                'id' => 'challenge-' . Str::random(6),
                'title' => 'Community Clean-Up',
                'description' => 'Volunteer for neighborhood clean-up activities',
                'goal' => '500 volunteer hours',
                'duration_days' => 60,
                'participants' => mt_rand(30, 150),
                'prize' => 'Community garden plot',
                'progress' => mt_rand(20, 60) . '% of goal reached',
                'started_at' => now()->subDays(mt_rand(1, 20))->toISOString(),
            ],
            [
                'id' => 'challenge-' . Str::random(6),
                'title' => 'Local Skill Sharing',
                'description' => 'Teach or learn 3 new skills from local community members',
                'goal' => 'Teach or learn 3 skills',
                'duration_days' => 90,
                'participants' => mt_rand(20, 100),
                'prize' => 'Community recognition award',
                'progress' => mt_rand(40, 70) . '% of participants active',
                'started_at' => now()->subDays(mt_rand(1, 30))->toISOString(),
            ],
        ];

        return [
            'challenges' => $challenges,
            'location' => $location,
            'total_challenges' => count($challenges),
            'featured' => true,
        ];
    }

    /**
     * Get local economic impact metrics
     */
    public function getLocalEconomicImpact(array $location): array
    {
        // These would be calculated from real data in a production environment
        $sampleData = [
            'local_business_growth' => mt_rand(5, 15) . '%',
            'local_spending_percentage' => mt_rand(25, 45) . '%',
            'new_local_jobs_created' => mt_rand(10, 50),
            'local_business_retention_rate' => mt_rand(75, 90) . '%',
            'average_local_business_age' => mt_rand(5, 20) . ' years',
            'local_supplier_percentage' => mt_rand(30, 60) . '%',
        ];

        return [
            'metrics' => $sampleData,
            'location' => $location,
            'calculated_at' => now()->toISOString(),
            'compared_to_region' => 'Above regional average',
            'trend' => 'Positive growth trend',
        ];
    }

    /**
     * Get volunteer opportunities
     */
    public function getVolunteerOpportunities(array $location, int $limit = 10): array
    {
        $opportunities = [];
        
        $orgs = [
            'Local Food Bank',
            'Community Garden',
            'Neighborhood Watch',
            'Local Library',
            'Senior Center',
            'Animal Shelter',
            'Environmental Group',
            'Youth Sports League',
        ];

        for ($i = 0; $i < min($limit, count($orgs)); $i++) {
            $org = $orgs[$i];
            $oppType = ['tutoring', 'cleaning', 'office work', 'event help', 'food serving'][array_rand(['tutoring', 'cleaning', 'office work', 'event help', 'food serving'])];
            
            $opportunities[] = [
                'id' => 'vol-' . Str::random(8),
                'organization' => $org,
                'opportunity' => ucfirst($oppType) . ' opportunity',
                'description' => "Volunteer to help with {$oppType} activities for {$org}",
                'location' => [
                    'name' => $org . ' Center',
                    'address' => $this->generateFakeAddress($location),
                ],
                'commitment' => mt_rand(1, 10) . ' hours per month',
                'ideal_for' => ['students', 'retirees', 'families', 'professionals'][array_rand(['students', 'retirees', 'families', 'professionals'])],
                'points_reward' => mt_rand(15, 35),
                'contact' => '+1-' . mt_rand(100, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(1000, 9999),
            ];
        }

        return [
            'opportunities' => $opportunities,
            'location' => $location,
            'total_opportunities' => count($opportunities),
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Join a community challenge
     */
    public function joinCommunityChallenge(string $userId, string $challengeId): array
    {
        // Verify user exists
        $user = \Cache::get("user_{$userId}");
        if (!$user) {
            throw new \InvalidArgumentException("User not found: {$userId}");
        }

        // In a real implementation, this would join the user to the challenge
        // For this implementation, we'll return a success response
        
        $participation = [
            'user_id' => $userId,
            'challenge_id' => $challengeId,
            'joined_at' => now()->toISOString(),
            'status' => 'active',
            'progress' => 0,
            'points_accumulated' => 0,
        ];

        // Store participation
        $participationKey = "challenge_participation_{$challengeId}_{$userId}";
        \Cache::put($participationKey, $participation, now()->addMonths(3));

        return [
            'success' => true,
            'participation' => $participation,
            'message' => 'Successfully joined the community challenge!',
        ];
    }
}