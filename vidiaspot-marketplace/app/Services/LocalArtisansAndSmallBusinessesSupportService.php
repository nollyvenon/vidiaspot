<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class LocalArtisansAndSmallBusinessesSupportService
{
    /**
     * Types of support offered to local artisans and small businesses
     */
    private array $supportTypes = [
        'promotional_support' => [
            'name' => 'Promotional Support',
            'description' => 'Increased visibility and marketing support',
            'benefits' => [
                'featured_listings',
                'promotional_campaigns',
                'social_media_shares',
                'newsletter_inclusion',
            ],
            'criteria' => [
                'local_business_verification',
                'quality_ratings',
                'community_engagement',
            ],
        ],
        'financial_services' => [
            'name' => 'Financial Services',
            'description' => 'Access to micro-loans and financial assistance',
            'benefits' => [
                'micro_loans',
                'payment_deferrals',
                'fee_reductions',
                'financial_counseling',
            ],
            'criteria' => [
                'business_plan',
                'creditworthiness',
                'local_impact',
            ],
        ],
        'educational_resources' => [
            'name' => 'Educational Resources',
            'description' => 'Training and educational materials for business growth',
            'benefits' => [
                'business_training_programs',
                'digital_literacy_courses',
                'marketing_workshops',
                'legal_guidance',
            ],
            'criteria' => [
                'willingness_to_learn',
                'commitment_to_business',
                'community_ties',
            ],
        ],
        'market_access' => [
            'name' => 'Market Access',
            'description' => 'Helping businesses reach wider markets',
            'benefits' => [
                'platform_listing',
                'bulk_order_programs',
                'wholesale_connections',
                'export_assistance',
            ],
            'criteria' => [
                'product_quality',
                'scalability',
                'reliability',
            ],
        ],
        'technical_support' => [
            'name' => 'Technical Support',
            'description' => 'Digital tools and technical assistance',
            'benefits' => [
                'free_or_discounted_tools',
                'e-commerce_setup',
                'inventory_management',
                'digital_payment_systems',
            ],
            'criteria' => [
                'technology_readiness',
                'business_size',
                'growth_potential',
            ],
        ],
        'networking_oppportunities' => [
            'name' => 'Networking Opportunities',
            'description' => 'Connecting businesses with partners and customers',
            'benefits' => [
                'business_meetups',
                'trade_shows',
                'mentorship_programs',
                'supplier_connectivity',
            ],
            'criteria' => [
                'active_participation',
                'collaboration_willingness',
                'business_funding',
            ],
        ],
    ];

    /**
     * Artisan categories
     */
    private array $artisanCategories = [
        'handmade_goods' => [
            'name' => 'Handmade Goods',
            'types' => ['jewelry', 'clothing', 'pottery', 'woodworking', 'textiles'],
            'requirements' => ['handmade_verification', 'sustainable_materials'],
        ],
        'services' => [
            'name' => 'Services',
            'types' => ['hairdressing', 'tailoring', 'repair', 'coaching', 'consulting'],
            'requirements' => ['skill_certification', 'experience_verification'],
        ],
        'food_and_beverage' => [
            'name' => 'Food & Beverage',
            'types' => ['bakery', 'restaurant', 'specialty_foods', 'brewing'],
            'requirements' => ['health_permits', 'food_safety_training'],
        ],
        'creative_arts' => [
            'name' => 'Creative Arts',
            'types' => ['painting', 'sculpture', 'photography', 'writing', 'design'],
            'requirements' => ['portfolio_submission', 'artistic_verification'],
        ],
    ];

    /**
     * Certification levels for artisans and small businesses
     */
    private array $certificationLevels = [
        'community_member' => [
            'name' => 'Community Member',
            'requirements' => ['registration', 'basic_profile'],
            'benefits' => ['platform_access', 'basic_tools'],
            'cost' => 0,
        ],
        'local_partner' => [
            'name' => 'Local Partner',
            'requirements' => ['local_verification', 'quality_rating_4+', 'community_feedback'],
            'benefits' => ['promoted_listings', 'priority_support', 'bulk_orders'],
            'cost' => 50,
        ],
        'green_business' => [
            'name' => 'Green Business',
            'requirements' => ['sustainability_practices', 'environmental_certification'],
            'benefits' => ['eco_badge', 'premium_promotion', 'partnership_opportunities'],
            'cost' => 100,
        ],
        'artisan_excellence' => [
            'name' => 'Artisan Excellence',
            'requirements' => ['craft_mastery', 'customer_satisfaction_95%+', 'tradition_preservation'],
            'benefits' => ['featured_artist_status', 'exclusive_markets', 'mentorship'],
            'cost' => 150,
        ],
        'community_champion' => [
            'name' => 'Community Champion',
            'requirements' => ['community_impact', 'employment_local', 'sustainability'],
            'benefits' => ['maximum_promotion', 'grant_eligibility', 'policy_consultation'],
            'cost' => 200,
        ],
    ];

    /**
     * Get support types available
     */
    public function getSupportTypes(): array
    {
        return $this->supportTypes;
    }

    /**
     * Get artisan categories
     */
    public function getArtisanCategories(): array
    {
        return $this->artisanCategories;
    }

    /**
     * Get certification levels
     */
    public function getCertificationLevels(): array
    {
        return $this->certificationLevels;
    }

    /**
     * Register an artisan or small business
     */
    public function registerBusiness(array $businessData, string $userId): array
    {
        $required = [
            'business_name',
            'description',
            'category',
            'sub_category',
            'owner_name',
            'contact_email',
            'contact_phone',
            'physical_address',
            'years_operating',
        ];

        foreach ($required as $field) {
            if (!isset($businessData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Validate category and sub-category
        if (!isset($this->artisanCategories[$businessData['category']])) {
            throw new \InvalidArgumentException("Invalid category: {$businessData['category']}");
        }

        $validSubCategories = $this->artisanCategories[$businessData['category']]['types'] ?? [];
        if (!in_array($businessData['sub_category'], $validSubCategories)) {
            throw new \InvalidArgumentException("Invalid sub-category: {$businessData['sub_category']}");
        }

        // Generate business ID
        $businessId = 'biz-' . Str::uuid();

        // Create business profile
        $businessProfile = [
            'id' => $businessId,
            'user_id' => $userId,
            'business_name' => $businessData['business_name'],
            'description' => $businessData['description'],
            'category' => $businessData['category'],
            'sub_category' => $businessData['sub_category'],
            'owner_name' => $businessData['owner_name'],
            'contact_email' => $businessData['contact_email'],
            'contact_phone' => $businessData['contact_phone'],
            'physical_address' => $businessData['physical_address'],
            'operating_since' => now()->subYears($businessData['years_operating'])->toISOString(),
            'years_operating' => $businessData['years_operating'],
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'status' => 'pending_verification',
            'certification_level' => 'community_member', // Start at basic level
            'rating' => 0,
            'reviews_count' => 0,
            'sustainability_score' => 0,
            'community_impact_score' => 0,
            'local_hiring_percentage' => $businessData['local_hiring_percentage'] ?? 0,
            'sustainable_practices' => $businessData['sustainable_practices'] ?? [],
            'products' => $businessData['products'] ?? [],
            'gallery' => $businessData['gallery'] ?? [],
            'certifications' => [],
        ];

        // Store business in cache (in real implementation, this would be in database)
        $cacheKey = "artisan_business_{$businessId}";
        \Cache::put($cacheKey, $businessProfile, now()->addMonths(24));

        // Add to user's business portfolio
        $userBusinessesKey = "user_businesses_{$userId}";
        $businesses = \Cache::get($userBusinessesKey, []);
        $businesses[] = $businessId;
        \Cache::put($userBusinessesKey, $businesses, now()->addMonths(24));

        return [
            'success' => true,
            'business_profile' => $businessProfile,
            'message' => 'Business registered successfully. Pending verification.',
        ];
    }

    /**
     * Get a business profile
     */
    public function getBusinessProfile(string $businessId): ?array
    {
        $cacheKey = "artisan_business_{$businessId}";
        return \Cache::get($cacheKey);
    }

    /**
     * Get all businesses in a category
     */
    public function getBusinessesByCategory(string $category, array $filters = []): array
    {
        // In a real implementation, this would query a database
        // For this implementation, we'll return sample data
        
        $sampleBusinesses = [
            [
                'id' => 'biz-' . Str::random(8),
                'business_name' => 'Smith Handcrafted Furniture',
                'category' => $category,
                'sub_category' => 'woodworking',
                'owner_name' => 'John Smith',
                'location' => 'Downtown District',
                'distance_miles' => mt_rand(1, 10),
                'rating' => round(mt_rand(70, 100) / 10, 1),
                'reviews_count' => mt_rand(10, 100),
                'certification_level' => 'local_partner',
                'sustainability_score' => mt_rand(70, 95),
                'community_impact_score' => mt_rand(75, 98),
                'description' => 'Handcrafted wooden furniture using sustainable practices',
                'gallery' => [mt_rand(1, 5) . '_furniture.jpg'],
                'featured' => true,
                'years_operating' => mt_rand(2, 15),
            ],
            [
                'id' => 'biz-' . Str::random(8),
                'business_name' => 'Maria\'s Embroidery Studio',
                'category' => $category,
                'sub_category' => 'textiles',
                'owner_name' => 'Maria Rodriguez',
                'location' => 'Historic Quarter',
                'distance_miles' => mt_rand(1, 15),
                'rating' => round(mt_rand(75, 100) / 10, 1),
                'reviews_count' => mt_rand(5, 50),
                'certification_level' => 'artisan_excellence',
                'sustainability_score' => mt_rand(80, 98),
                'community_impact_score' => mt_rand(85, 99),
                'description' => 'Traditional embroidery and textile arts with modern designs',
                'gallery' => [mt_rand(1, 5) . '_embroidery.jpg'],
                'featured' => true,
                'years_operating' => mt_rand(5, 20),
            ],
            [
                'id' => 'biz-' . Str::random(8),
                'business_name' => 'Green Groceries Market',
                'category' => $category,
                'sub_category' => 'specialty_foods',
                'owner_name' => 'Aisha Johnson',
                'location' => 'Riverside Area',
                'distance_miles' => mt_rand(1, 12),
                'rating' => round(mt_rand(80, 100) / 10, 1),
                'reviews_count' => mt_rand(20, 80),
                'certification_level' => 'green_business',
                'sustainability_score' => mt_rand(90, 100),
                'community_impact_score' => mt_rand(75, 95),
                'description' => 'Locally sourced and organic groceries',
                'gallery' => [mt_rand(1, 5) . '_grocery.jpg'],
                'featured' => true,
                'years_operating' => mt_rand(1, 8),
            ],
        ];

        // Filter by sub-category if specified
        if (!empty($filters['sub_category'])) {
            $sampleBusinesses = array_filter($sampleBusinesses, function($biz) use ($filters) {
                return $biz['sub_category'] === $filters['sub_category'];
            });
        }

        // Apply other filters
        if (!empty($filters['min_rating'])) {
            $sampleBusinesses = array_filter($sampleBusinesses, function($biz) use ($filters) {
                return $biz['rating'] >= $filters['min_rating'];
            });
        }

        if (!empty($filters['certification_level'])) {
            $sampleBusinesses = array_filter($sampleBusinesses, function($biz) use ($filters) {
                return $biz['certification_level'] === $filters['certification_level'];
            });
        }

        return [
            'businesses' => array_values($sampleBusinesses),
            'total_count' => count($sampleBusinesses),
            'category' => $category,
            'filters' => $filters,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Find artisans near a user's location
     */
    public function findArtisansNearbyLocation(array $location, string $category = null, int $radiusMiles = 15): array
    {
        // In a real implementation, this would use geolocation to find nearby businesses
        // For this implementation, we'll return sample data
        
        $categories = $category ? [$category] : array_keys($this->artisanCategories);
        $allBusinesses = [];

        foreach ($categories as $cat) {
            $categoryBusinesses = $this->getBusinessesByCategory($cat);
            $allBusinesses = array_merge($allBusinesses, $categoryBusinesses['businesses']);
        }

        // Filter by radius (in this implementation, we'll pretend all are within range)
        $nearbyBusinesses = array_filter($allBusinesses, function($biz) use ($radiusMiles) {
            return $biz['distance_miles'] <= $radiusMiles;
        });

        return [
            'businesses' => array_values($nearbyBusinesses),
            'total_count' => count($nearbyBusinesses),
            'location' => $location,
            'search_radius_miles' => $radiusMiles,
            'categories_searched' => $categories,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Apply for business certification
     */
    public function applyForCertification(string $businessId, string $certificationLevel, array $documentation): array
    {
        $business = $this->getBusinessProfile($businessId);
        if (!$business) {
            throw new \InvalidArgumentException("Business not found: {$businessId}");
        }

        if (!isset($this->certificationLevels[$certificationLevel])) {
            throw new \InvalidArgumentException("Invalid certification level: {$certificationLevel}");
        }

        // Check if business meets requirements for certification
        $requirements = $this->certificationLevels[$certificationLevel]['requirements'];
        $requirementsMet = $this->checkCertificationRequirements($business, $requirements);

        // Create certification application
        $applicationId = 'cert-' . Str::uuid();
        $application = [
            'id' => $applicationId,
            'business_id' => $businessId,
            'certification_level' => $certificationLevel,
            'documentation' => $documentation,
            'requirements_met' => $requirementsMet['met'],
            'requirements_missing' => $requirementsMet['missing'],
            'status' => count($requirementsMet['missing']) === 0 ? 'approved' : 'pending_documentation',
            'applied_at' => now()->toISOString(),
            'review_comments' => $requirementsMet['comments'],
        ];

        // Store application
        $cacheKey = "certification_application_{$applicationId}";
        \Cache::put($cacheKey, $application, now()->addMonths(6));

        // Update business profile if approved
        if ($application['status'] === 'approved') {
            $business['certification_level'] = $certificationLevel;
            
            // Add certification to business
            $business['certifications'][] = [
                'level' => $certificationLevel,
                'awarded_at' => now()->toISOString(),
                'application_id' => $applicationId,
            ];
            
            $updateKey = "artisan_business_{$businessId}";
            \Cache::put($updateKey, $business, now()->addMonths(24));
        }

        return [
            'application' => $application,
            'business_updated' => $application['status'] === 'approved',
            'message' => $application['status'] === 'approved' ? 
                        "Certification {$certificationLevel} approved!" : 
                        "Application submitted. Awaiting documentation.",
        ];
    }

    /**
     * Check if business meets certification requirements
     */
    private function checkCertificationRequirements(array $business, array $requirements): array
    {
        $met = [];
        $missing = [];
        $comments = [];

        foreach ($requirements as $req) {
            $isMet = false;
            
            switch ($req) {
                case 'local_verification':
                    $isMet = !empty($business['physical_address']);
                    break;
                case 'quality_rating_4+':
                    $isMet = $business['rating'] >= 4.0;
                    break;
                case 'community_feedback':
                    $isMet = $business['reviews_count'] >= 5;
                    break;
                case 'sustainability_practices':
                    $isMet = !empty($business['sustainable_practices']);
                    break;
                case 'craft_mastery':
                    $isMet = $business['years_operating'] >= 5;
                    break;
                case 'customer_satisfaction_95%+':
                    $isMet = ($business['rating'] / 5) * 100 >= 95;
                    break;
                case 'community_impact':
                    $isMet = $business['community_impact_score'] >= 75;
                    break;
                case 'employment_local':
                    $isMet = $business['local_hiring_percentage'] >= 50;
                    break;
            }

            if ($isMet) {
                $met[] = $req;
            } else {
                $missing[] = $req;
                
                // Add specific guidance
                switch ($req) {
                    case 'quality_rating_4+':
                        $comments[] = "Current rating is {$business['rating']}/5.0. Need 4.0+ for this certification.";
                        break;
                    case 'community_feedback':
                        $comments[] = "Need at least 5 reviews. Current: {$business['reviews_count']}.";
                        break;
                    case 'sustainability_practices':
                        $comments[] = "Add information about sustainable practices to your profile.";
                        break;
                }
            }
        }

        return [
            'met' => $met,
            'missing' => $missing,
            'comments' => $comments,
        ];
    }

    /**
     * Get eligible support programs for a business
     */
    public function getEligibleSupportPrograms(string $businessId): array
    {
        $business = $this->getBusinessProfile($businessId);
        if (!$business) {
            throw new \InvalidArgumentException("Business not found: {$businessId}");
        }

        $eligiblePrograms = [];

        foreach ($this->supportTypes as $programId => $program) {
            $isEligible = true;
            
            foreach ($program['criteria'] as $criterion) {
                switch ($criterion) {
                    case 'local_business_verification':
                        $isEligible &= !empty($business['physical_address']);
                        break;
                    case 'quality_ratings':
                        $isEligible &= $business['rating'] > 3.5;
                        break;
                    case 'community_engagement':
                        $isEligible &= $business['reviews_count'] > 2;
                        break;
                    case 'sustainability_practices':
                        $isEligible &= !empty($business['sustainable_practices']);
                        break;
                    case 'product_quality':
                        $isEligible &= $business['rating'] >= 4.0;
                        break;
                }
                
                if (!$isEligible) {
                    break; // No need to check other criteria if already ineligible
                }
            }
            
            if ($isEligible) {
                $program['id'] = $programId;
                $eligiblePrograms[] = $program;
            }
        }

        return [
            'business_id' => $businessId,
            'eligible_programs' => $eligiblePrograms,
            'total_programs' => count($eligiblePrograms),
            'all_support_types' => $this->supportTypes,
        ];
    }

    /**
     * Apply for a support program
     */
    public function applyForSupportProgram(string $businessId, string $programId, array $applicationData): array
    {
        if (!isset($this->supportTypes[$programId])) {
            throw new \InvalidArgumentException("Invalid program ID: {$programId}");
        }

        $business = $this->getBusinessProfile($businessId);
        if (!$business) {
            throw new \InvalidArgumentException("Business not found: {$businessId}");
        }

        // Check eligibility again
        $programs = $this->getEligibleSupportPrograms($businessId);
        $isEligible = false;
        
        foreach ($programs['eligible_programs'] as $prog) {
            if ($prog['id'] === $programId) {
                $isEligible = true;
                break;
            }
        }

        if (!$isEligible) {
            throw new \InvalidArgumentException("Business is not eligible for program: {$programId}");
        }

        // Generate application ID
        $applicationId = 'support-app-' . Str::uuid();
        
        $application = [
            'id' => $applicationId,
            'business_id' => $businessId,
            'program_id' => $programId,
            'application_data' => $applicationData,
            'submitted_at' => now()->toISOString(),
            'status' => 'under_review',
            'review_deadline' => now()->addDays(14)->toISOString(),
            'assigned_reviewer' => null,
            'feedback' => null,
        ];

        // Store application
        $cacheKey = "support_application_{$applicationId}";
        \Cache::put($cacheKey, $application, now()->addMonths(6));

        // Add to business applications list
        $businessApplnsKey = "business_support_applications_{$businessId}";
        $applications = \Cache::get($businessApplnsKey, []);
        $applications[] = $applicationId;
        \Cache::put($businessApplnsKey, $applications, now()->addMonths(6));

        return [
            'success' => true,
            'application' => $application,
            'message' => 'Support program application submitted successfully',
        ];
    }

    /**
     * Get business dashboard metrics
     */
    public function getBusinessDashboardMetrics(string $businessId): array
    {
        $business = $this->getBusinessProfile($businessId);
        if (!$business) {
            throw new \InvalidArgumentException("Business not found: {$businessId}");
        }

        // In a real implementation, this would aggregate real data
        // For this implementation, we'll return sample metrics
        $metrics = [
            'views' => mt_rand(500, 5000),
            'inquiries' => mt_rand(10, 100),
            'sales' => mt_rand(5, 50),
            'conversion_rate' => round(mt_rand(20, 150) / 10, 1),
            'average_rating' => $business['rating'],
            'reviews_count' => $business['reviews_count'],
            'followers' => mt_rand(50, 500),
            'engagement_rate' => round(mt_rand(10, 80) / 10, 1),
            'sustainability_score' => $business['sustainability_score'],
            'community_impact_score' => $business['community_impact_score'],
        ];

        return [
            'business_id' => $businessId,
            'business_name' => $business['business_name'],
            'certification_level' => $business['certification_level'],
            'current_month_metrics' => $metrics,
            'trend_comparison' => [
                'views_change' => mt_rand(-10, 30) . '%',
                'inquiries_change' => mt_rand(-5, 25) . '%',
                'sales_change' => mt_rand(-15, 20) . '%',
            ],
            'recommendations' => $this->generateBusinessRecommendations($metrics),
            'announcements' => [
                'new_support_program_available' => true,
                'certification_opportunity' => $business['certification_level'] !== 'community_champion',
            ],
        ];
    }

    /**
     * Generate business recommendations
     */
    private function generateBusinessRecommendations(array $metrics): array
    {
        $recommendations = [];

        if ($metrics['conversion_rate'] < 5) {
            $recommendations[] = [
                'type' => 'sales',
                'title' => 'Improve Conversion Rate',
                'description' => 'Your current conversion rate is low. Consider optimizing your product descriptions.',
                'priority' => 'high',
            ];
        }

        if ($metrics['average_rating'] < 4.0) {
            $recommendations[] = [
                'type' => 'quality',
                'title' => 'Enhance Customer Satisfaction',
                'description' => 'Work on improving your customer service to boost ratings.',
                'priority' => 'medium',
            ];
        }

        if ($metrics['reviews_count'] < 10) {
            $recommendations[] = [
                'type' => 'engagement',
                'title' => 'Encourage Reviews',
                'description' => 'Ask satisfied customers to leave reviews to build credibility.',
                'priority' => 'medium',
            ];
        }

        if ($metrics['sustainability_score'] < 75) {
            $recommendations[] = [
                'type' => 'sustainability',
                'title' => 'Implement Sustainable Practices',
                'description' => 'Consider eco-friendly packaging or local sourcing to improve sustainability.',
                'priority' => 'low',
            ];
        }

        return $recommendations;
    }

    /**
     * Get artisan directory with filtering options
     */
    public function getArtisanDirectory(array $filters = []): array
    {
        // This would normally query a database of all artisans
        // For this implementation, we'll generate a comprehensive sample
        $allBusinesses = [];
        $categories = array_keys($this->artisanCategories);
        
        foreach ($categories as $category) {
            $catBusinesses = $this->getBusinessesByCategory($category);
            $allBusinesses = array_merge($allBusinesses, $catBusinesses['businesses']);
        }

        // Apply filters
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $allBusinesses = array_filter($allBusinesses, function($biz) use ($search) {
                return strpos(strtolower($biz['business_name']), $search) !== false ||
                       strpos(strtolower($biz['owner_name']), $search) !== false ||
                       strpos(strtolower($biz['description']), $search) !== false;
            });
        }

        if (!empty($filters['min_sustainability_score'])) {
            $allBusinesses = array_filter($allBusinesses, function($biz) use ($filters) {
                return $biz['sustainability_score'] >= $filters['min_sustainability_score'];
            });
        }

        if (!empty($filters['local_hiring_preference'])) {
            $allBusinesses = array_filter($allBusinesses, function($biz) use ($filters) {
                return $biz['local_hiring_percentage'] >= ($filters['local_hiring_preference'] ?? 50);
            });
        }

        // Sort by rating (descending) as default
        usort($allBusinesses, function($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });

        return [
            'artisans' => array_values($allBusinesses),
            'total_count' => count($allBusinesses),
            'filters_applied' => $filters,
            'categories' => $categories,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get business resource center content
     */
    public function getResourceCenterContent(array $filters = []): array
    {
        $content = [
            [
                'id' => 'rc-' . Str::random(6),
                'title' => 'Marketing Your Small Business Online',
                'category' => 'marketing',
                'sub_category' => 'digital',
                'type' => 'article',
                'author' => 'Business Development Team',
                'date' => now()->subDays(3)->toISOString(),
                'read_time_minutes' => 8,
                'summary' => 'Learn how to leverage social media and online platforms to grow your small business.',
                'tags' => ['social_media', 'online_marketing', 'branding'],
                'difficulty' => 'beginner',
                'target_audience' => ['small_business', 'startup'],
            ],
            [
                'id' => 'rc-' . Str::random(6),
                'title' => 'Sustainable Manufacturing Practices',
                'category' => 'sustainability',
                'sub_category' => 'production',
                'type' => 'guide',
                'author' => 'Sustainability Committee',
                'date' => now()->subWeek()->toISOString(),
                'read_time_minutes' => 15,
                'summary' => 'Implement eco-friendly practices in your production process to reduce environmental impact.',
                'tags' => ['environment', 'manufacturing', 'waste_reduction'],
                'difficulty' => 'intermediate',
                'target_audience' => ['manufacturer', 'producer'],
            ],
            [
                'id' => 'rc-' . Str::random(6),
                'title' => 'Accessing Microfinance for Your Business',
                'category' => 'finance',
                'sub_category' => 'funding',
                'type' => 'video',
                'author' => 'Financial Inclusion Team',
                'date' => now()->subDays(10)->toISOString(),
                'duration_minutes' => 12,
                'summary' => 'Guidance on applying for microloans and alternative financing options.',
                'tags' => ['funding', 'microfinance', 'loan_application'],
                'difficulty' => 'beginner',
                'target_audience' => ['startup', 'small_business'],
            ],
            [
                'id' => 'rc-' . Str::random(6),
                'title' => 'Legal Requirements for Small Businesses',
                'category' => 'legal',
                'sub_category' => 'compliance',
                'type' => 'checklist',
                'author' => 'Legal Advisory Team',
                'date' => now()->subDays(15)->toISOString(),
                'read_time_minutes' => 5,
                'summary' => 'Essential legal steps every small business owner should take.',
                'tags' => ['legal', 'compliance', 'registration'],
                'difficulty' => 'beginner',
                'target_audience' => ['all'],
            ],
        ];

        // Apply filters
        if (!empty($filters['category'])) {
            $content = array_filter($content, function($item) use ($filters) {
                return $item['category'] === $filters['category'];
            });
        }

        if (!empty($filters['difficulty'])) {
            $content = array_filter($content, function($item) use ($filters) {
                return $item['difficulty'] === $filters['difficulty'];
            });
        }

        return [
            'resources' => array_values($content),
            'total_count' => count($content),
            'categories' => array_unique(array_column($content, 'category')),
            'filters' => $filters,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get community impact metrics for local businesses
     */
    public function getCommunityImpactMetrics(array $location, array $filters = []): array
    {
        // In a real implementation, this would aggregate data from all local businesses
        // For this implementation, we'll return sample metrics
        
        $metrics = [
            'total_local_businesses' => mt_rand(50, 500),
            'jobs_created' => mt_rand(200, 2000),
            'local_sourcing_percentage' => mt_rand(40, 80) . '%',
            'average_business_tenure_years' => mt_rand(3, 8),
            'revenue_generated_local' => '$' . mt_rand(5, 50) . 'M annually',
            'environmental_impact_score' => mt_rand(65, 85),
            'community_engagement_score' => mt_rand(70, 90),
            'diversity_inclusion_score' => mt_rand(75, 95),
            'local_supplier_percentage' => mt_rand(45, 75) . '%',
            'employees_from_local_area' => mt_rand(60, 90) . '%',
        ];

        return [
            'location' => $location,
            'community_impact' => $metrics,
            'filters' => $filters,
            'comparison_data' => [
                'regional_average' => [
                    'local_sourcing_percentage' => mt_rand(25, 45) . '%',
                    'average_business_tenure_years' => mt_rand(2, 5),
                    'environmental_impact_score' => mt_rand(40, 65),
                ],
                'national_average' => [
                    'local_sourcing_percentage' => mt_rand(20, 40) . '%',
                    'average_business_tenure_years' => mt_rand(2, 4),
                    'environmental_impact_score' => mt_rand(35, 60),
                ],
            ],
            'calculated_at' => now()->toISOString(),
        ];
    }
}