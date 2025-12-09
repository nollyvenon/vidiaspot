<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class WomenAndMinorityEntrepreneurSupportService
{
    /**
     * Types of support specifically for women and minority entrepreneurs
     */
    private array $supportPrograms = [
        'mentorship_network' => [
            'name' => 'Leadership Mentorship Program',
            'description' => 'Connect with successful women and minority business leaders',
            'target_audience' => ['women', 'minority'],
            'eligibility' => [
                'ownership_percentage' => 51,
                'business_age_min_years' => 1,
                'revenue_threshold' => 10000,
            ],
            'benefits' => [
                'one_on_one_mentoring',
                'group_sessions',
                'networking_events',
                'resource_library',
            ],
            'time_commitment' => '30-60 mins/week',
            'duration' => '6 months',
        ],
        'capital_access_fund' => [
            'name' => 'Capital Access Fund',
            'description' => 'Micro-loans and grants specifically for women and minority entrepreneurs',
            'target_audience' => ['women', 'minority'],
            'eligibility' => [
                'ownership_percentage' => 51,
                'business_plan_required' => true,
                'credit_score_min' => 550,
                'community_impact_score' => 70,
            ],
            'benefits' => [
                'preferential_interest_rates',
                'grants_up_to_50k',
                'loan_amounts_500_50000',
                'financial_literacy_training',
            ],
            'time_commitment' => 'Variable',
            'duration' => 'Loan dependent',
        ],
        'business_incubator' => [
            'name' => 'Women & Minority Business Incubator',
            'description' => 'Accelerated startup support program',
            'target_audience' => ['women', 'minority'],
            'eligibility' => [
                'ownership_percentage' => 51,
                'innovation_score' => 80,
                'scalability_assessment' => 75,
                'team_diversity' => 50,
            ],
            'benefits' => [
                'workspace_access',
                'legal_support',
                'marketing_assistance',
                'pitch_training',
                'investor_connections',
            ],
            'time_commitment' => 'Full-time during program',
            'duration' => '3-6 months',
        ],
        'training_workshops' => [
            'name' => 'Specialized Training Workshops',
            'description' => 'Business skills development tailored to unique challenges',
            'target_audience' => ['women', 'minority'],
            'eligibility' => [
                'business_registration_required' => true,
                'operating_time_min_months' => 6,
            ],
            'benefits' => [
                'leadership_development',
                'negotiation_skills',
                'funding_strategies',
                'digital_marketing',
                'scaling_secrets',
            ],
            'time_commitment' => '4-8 hours/month',
            'duration' => 'Ongoing monthly sessions',
        ],
        'procurement_opportunities' => [
            'name' => 'Supplier Diversity Program',
            'description' => 'Connect businesses with procurement opportunities',
            'target_audience' => ['women', 'minority'],
            'eligibility' => [
                'certification_required' => true,
                'annual_revenue_min' => 50000,
                'business_age_min_years' => 2,
            ],
            'benefits' => [
                'bid_opportunities',
                'contract_matching',
                'supplier_showcases',
                'corporate_partnerships',
            ],
            'time_commitment' => 'As needed',
            'duration' => 'Ongoing',
        ],
        'legal_advocacy' => [
            'name' => 'Legal Advocacy & Support',
            'description' => 'Legal resources and advocacy for equitable treatment',
            'target_audience' => ['women', 'minority'],
            'eligibility' => [
                'discrimination_complaint' => true,
                'legal_issue_type' => ['funding', 'contracts', 'workplace', 'business_licensing'],
            ],
            'benefits' => [
                'legal_consultation',
                'advocacy_services',
                'rights_awareness',
                'policy_engagement',
            ],
            'time_commitment' => 'As needed',
            'duration' => 'Case dependent',
        ],
        'networking_conferences' => [
            'name' => 'Women & Minority Entrepreneur Conferences',
            'description' => 'Annual and quarterly networking events',
            'target_audience' => ['women', 'minority'],
            'eligibility' => [
                'business_ownership' => true,
                'registration_required' => true,
            ],
            'benefits' => [
                'high_level_connections',
                'investment_opportunities',
                'media_exposure',
                'award_recognition',
            ],
            'time_commitment' => '2-3 days annually',
            'duration' => 'Annual + quarterly',
        ],
    ];

    /**
     * Certification and verification requirements
     */
    private array $certificationRequirements = [
        'women_owned_business' => [
            'name' => 'Women-Owned Business Enterprise (WOBE)',
            'issuing_body' => 'Women\'s Business Enterprise National Council (WBENC)',
            'requirements' => [
                'female_ownership_min' => 51,
                'female_control' => true,
                'independence' => true,
                'profit_motivated' => true,
            ],
            'verification_process' => 'Third-party certification',
            'cost' => '$500-$1500',
        ],
        'minority_owned_business' => [
            'name' => 'Minority Business Enterprise (MBE)',
            'issuing_body' => 'National Minority Supplier Development Council (NMSDC)',
            'requirements' => [
                'minority_ownership_min' => 51,
                'minority_control' => true,
                'independence' => true,
                'us_citizenship' => true,
            ],
            'verification_process' => 'Third-party certification',
            'cost' => '$400-$1200',
        ],
        'disabled_veteran_owned_business' => [
            'name' => 'Service-Disabled Veteran-Owned Small Business (SDVOSB)',
            'issuing_body' => 'US Department of Veterans Affairs',
            'requirements' => [
                'veteran_disabled_ownership' => 51,
                'service_connected_disability' => true,
                'independence' => true,
            ],
            'verification_process' => 'Government verification',
            'cost' => 'Free',
        ],
    ];

    /**
     * Resources specifically for women and minority entrepreneurs
     */
    private array $resources = [
        'funding_resources' => [
            'name' => 'Specialized Funding Resources',
            'content' => [
                'grants_for_women_minorities' => [
                    'title' => 'Grants for Women and Minorities',
                    'description' => 'List of funding opportunities specifically for underrepresented entrepreneurs',
                    'links' => [
                        'https://www.womensbusinesscenter.gov/',
                        'https://www.mbda.gov/',
                        'https://www.score.org/',
                    ],
                ],
                'venture_capital_funds' => [
                    'title' => 'VC Funds Supporting Diversity',
                    'description' => 'Venture capital firms with diversity mandates',
                    'links' => [
                        'https://www.colorinvested.com/',
                        'https://www.diversityvc.com/',
                    ],
                ],
            ],
        ],
        'legal_resources' => [
            'name' => 'Legal Resources',
            'content' => [
                'business_formation' => [
                    'title' => 'Business Formation Guides',
                    'description' => 'Legal guides for starting a business as an underrepresented founder',
                    'links' => [
                        'https://www.sba.gov/',
                        'https://www.nolo.com/',
                    ],
                ],
                'contract_review' => [
                    'title' => 'Contract Review Resources',
                    'description' => 'Resources for ensuring fair contracts and agreements',
                    'links' => [
                        'https://www.americanbar.org/groups/diversity/',
                    ],
                ],
            ],
        ],
        'networking_platforms' => [
            'name' => 'Networking Platforms',
            'content' => [
                'professional_associations' => [
                    'title' => 'Professional Associations',
                    'description' => 'Organizations specifically for women and minority entrepreneurs',
                    'links' => [
                        'https://www.womenable.com/',
                        'https://www.minoritybusinesslists.com/',
                    ],
                ],
            ],
        ],
    ];

    /**
     * Get all support programs
     */
    public function getSupportPrograms(): array
    {
        return $this->supportPrograms;
    }

    /**
     * Get certification requirements
     */
    public function getCertificationRequirements(): array
    {
        return $this->certificationRequirements;
    }

    /**
     * Get specialized resources
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Register a business for women/minority support programs
     */
    public function registerForSupport(array $businessData, string $userId, string $demographic): array
    {
        // Validate demographic
        $validDemographics = ['women', 'minority', 'veteran', 'disabled', 'lgbtq'];
        if (!in_array($demographic, $validDemographics)) {
            throw new \InvalidArgumentException("Invalid demographic: {$demographic}");
        }

        // Validate required fields
        $required = [
            'business_name',
            'owner_name',
            'demographic_identity',
            'business_type',
            'years_operating',
            'annual_revenue',
            'ownership_percentage',
            'contact_email',
            'physical_address',
        ];

        foreach ($required as $field) {
            if (!isset($businessData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Validate ownership percentage is sufficient for programs
        if ($businessData['ownership_percentage'] < 51) {
            throw new \InvalidArgumentException("Ownership must be at least 51% for qualification");
        }

        // Generate business profile ID
        $profileId = 'wmep-' . Str::uuid();

        // Create business profile with demographic information
        $profile = [
            'id' => $profileId,
            'user_id' => $userId,
            'business_name' => $businessData['business_name'],
            'owner_name' => $businessData['owner_name'],
            'demographic_identity' => $businessData['demographic_identity'],
            'primary_demographic' => $demographic,
            'business_type' => $businessData['business_type'],
            'years_operating' => $businessData['years_operating'],
            'annual_revenue' => $businessData['annual_revenue'],
            'ownership_percentage' => $businessData['ownership_percentage'],
            'contact_email' => $businessData['contact_email'],
            'physical_address' => $businessData['physical_address'],
            'certification_status' => 'pending',
            'certification_requirements_met' => [],
            'eligible_programs' => $this->checkEligiblePrograms($businessData),
            'support_level' => $this->determineSupportLevel($businessData),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'status' => 'pending_verification',
        ];

        // Store in cache (in real implementation, this would be in database)
        $cacheKey = "wmep_profile_{$profileId}";
        \Cache::put($cacheKey, $profile, now()->addMonths(24));

        // Add to user's profiles
        $userProfilesKey = "user_wmep_profiles_{$userId}";
        $profiles = \Cache::get($userProfilesKey, []);
        $profiles[] = $profileId;
        \Cache::put($userProfilesKey, $profiles, now()->addMonths(24));

        return [
            'success' => true,
            'profile' => $profile,
            'message' => 'Registration for women and minority entrepreneur programs completed. Profile pending verification.',
        ];
    }

    /**
     * Check which programs a business is eligible for
     */
    private function checkEligiblePrograms(array $businessData): array
    {
        $eligible = [];

        foreach ($this->supportPrograms as $programId => $program) {
            $isEligible = true;
            
            foreach ($program['eligibility'] as $requirement => $value) {
                switch ($requirement) {
                    case 'ownership_percentage':
                        if ($businessData['ownership_percentage'] < $value) {
                            $isEligible = false;
                        }
                        break;
                    case 'business_age_min_years':
                        if ($businessData['years_operating'] < $value) {
                            $isEligible = false;
                        }
                        break;
                    case 'revenue_threshold':
                        if ($businessData['annual_revenue'] < $value) {
                            $isEligible = false;
                        }
                        break;
                    case 'business_plan_required':
                        if ($value && empty($businessData['business_plan'])) {
                            $isEligible = false;
                        }
                        break;
                    case 'credit_score_min':
                        if (isset($businessData['credit_score']) && $businessData['credit_score'] < $value) {
                            $isEligible = false;
                        }
                        break;
                    case 'community_impact_score':
                        if (isset($businessData['community_impact_score']) && $businessData['community_impact_score'] < $value) {
                            $isEligible = false;
                        }
                        break;
                }
                
                if (!$isEligible) {
                    break;
                }
            }
            
            if ($isEligible) {
                $eligible[] = $programId;
            }
        }

        return $eligible;
    }

    /**
     * Determine support level based on business characteristics
     */
    private function determineSupportLevel(array $businessData): string
    {
        $score = 0;
        
        // Score based on business maturity
        $score += $businessData['years_operating'] * 10;
        
        // Score based on revenue
        $score += min(30, $businessData['annual_revenue'] / 10000);
        
        // Score based on ownership percentage
        $score += ($businessData['ownership_percentage'] - 50) / 10;
        
        // Score based on business type (some types need more support)
        if (in_array($businessData['business_type'], ['technology', 'manufacturing', 'export'])) {
            $score += 15;
        }

        if ($score >= 50) {
            return 'advanced';
        } elseif ($score >= 25) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    /**
     * Get a business profile
     */
    public function getBusinessProfile(string $profileId): ?array
    {
        $cacheKey = "wmep_profile_{$profileId}";
        return \Cache::get($cacheKey);
    }

    /**
     * Apply for a specific support program
     */
    public function applyForSupportProgram(string $profileId, string $programId, array $applicationData): array
    {
        if (!isset($this->supportPrograms[$programId])) {
            throw new \InvalidArgumentException("Invalid program ID: {$programId}");
        }

        $profile = $this->getBusinessProfile($profileId);
        if (!$profile) {
            throw new \InvalidArgumentException("Business profile not found: {$profileId}");
        }

        // Check if business is eligible for this program
        if (!in_array($programId, $profile['eligible_programs'])) {
            throw new \InvalidArgumentException("Business is not eligible for program: {$programId}");
        }

        // Generate application ID
        $applicationId = 'wmep-apply-' . Str::uuid();

        $application = [
            'id' => $applicationId,
            'profile_id' => $profileId,
            'program_id' => $programId,
            'program_name' => $this->supportPrograms[$programId]['name'],
            'application_data' => $applicationData,
            'submitted_at' => now()->toISOString(),
            'status' => 'under_review',
            'review_deadline' => now()->addDays(21)->toISOString(),
            'assigned_specialist' => null,
            'next_steps' => $this->getNextStepsForProgram($programId),
        ];

        // Store application
        $cacheKey = "wmep_application_{$applicationId}";
        \Cache::put($cacheKey, $application, now()->addMonths(6));

        // Add to profile's applications
        $profileApplicationsKey = "wmep_profile_applications_{$profileId}";
        $applications = \Cache::get($profileApplicationsKey, []);
        $applications[] = $applicationId;
        \Cache::put($profileApplicationsKey, $applications, now()->addMonths(6));

        // Update profile status
        $profile['status'] = 'application_submitted';
        $updateKey = "wmep_profile_{$profileId}";
        \Cache::put($updateKey, $profile, now()->addMonths(24));

        return [
            'success' => true,
            'application' => $application,
            'message' => 'Support program application submitted successfully',
        ];
    }

    /**
     * Get next steps for a specific program
     */
    private function getNextStepsForProgram(string $programId): array
    {
        $steps = [];

        switch ($programId) {
            case 'mentorship_network':
                $steps = [
                    'Application review (1 week)',
                    'Matching with mentors (2 weeks)',
                    'Initial meeting setup (1 week)',
                    '6-month mentoring program begins',
                ];
                break;
            case 'capital_access_fund':
                $steps = [
                    'Credit check and documentation review (1 week)',
                    'Business plan evaluation (1 week)',
                    'Loan committee review (1 week)',
                    'Final approval and funding',
                ];
                break;
            case 'business_incubator':
                $steps = [
                    'Interview and selection process (2 weeks)',
                    'Program orientation (1 week)',
                    'Team assignment (1 week)',
                    '3-6 month intensive program begins',
                ];
                break;
            case 'training_workshops':
                $steps = [
                    'Assessment of current skills (1 week)',
                    'Customized curriculum planning',
                    'Monthly workshop series begins',
                ];
                break;
            case 'procurement_opportunities':
                $steps = [
                    'Certification verification (2 weeks)',
                    'Supplier portal access setup (1 week)',
                    'Opportunity matching begins',
                ];
                break;
            default:
                $steps = [
                    'Application review process begins',
                    'Communication regarding next steps',
                ];
        }

        return $steps;
    }

    /**
     * Get available certification options
     */
    public function getAvailableCertifications(string $demographic = null): array
    {
        if ($demographic) {
            $certs = [];
            $demographicMap = [
                'women' => ['women_owned_business'],
                'minority' => ['minority_owned_business'],
                'veteran' => ['disabled_veteran_owned_business'],
                'all' => array_keys($this->certificationRequirements),
            ];

            $types = $demographicMap[$demographic] ?? $demographicMap['all'];

            foreach ($types as $type) {
                if (isset($this->certificationRequirements[$type])) {
                    $certs[$type] = $this->certificationRequirements[$type];
                }
            }

            return $certs;
        }

        return $this->certificationRequirements;
    }

    /**
     * Apply for business certification
     */
    public function applyForCertification(string $profileId, string $certificationType, array $documentation): array
    {
        if (!isset($this->certificationRequirements[$certificationType])) {
            throw new \InvalidArgumentException("Invalid certification type: {$certificationType}");
        }

        $profile = $this->getBusinessProfile($profileId);
        if (!$profile) {
            throw new \InvalidArgumentException("Business profile not found: {$profileId}");
        }

        // Generate certification application ID
        $applicationId = 'cert-app-' . Str::uuid();

        $application = [
            'id' => $applicationId,
            'profile_id' => $profileId,
            'certification_type' => $certificationType,
            'certification_name' => $this->certificationRequirements[$certificationType]['name'],
            'issuing_body' => $this->certificationRequirements[$certificationType]['issuing_body'],
            'documentation_provided' => array_keys($documentation),
            'submitted_at' => now()->toISOString(),
            'status' => 'pending_verification',
            'estimated_processing_time' => '30-60 days',
            'fees' => $this->certificationRequirements[$certificationType]['cost'],
            'requirements_met' => $this->verifyCertificationRequirements($profile, $certificationType, $documentation),
        ];

        // Store application
        $cacheKey = "wmep_cert_app_{$applicationId}";
        \Cache::put($cacheKey, $application, now()->addMonths(12));

        // Add to profile's certifications
        $profileCertsKey = "wmep_profile_certifications_{$profileId}";
        $applications = \Cache::get($profileCertsKey, []);
        $applications[] = $applicationId;
        \Cache::put($profileCertsKey, $applications, now()->addMonths(12));

        return [
            'success' => true,
            'application' => $application,
            'message' => 'Certification application submitted successfully',
        ];
    }

    /**
     * Verify certification requirements are met
     */
    private function verifyCertificationRequirements(array $profile, string $certificationType, array $documentation): array
    {
        $requirements = $this->certificationRequirements[$certificationType]['requirements'];
        $verification = [];

        foreach ($requirements as $requirement => $value) {
            switch ($requirement) {
                case 'female_ownership_min':
                    $verification[$requirement] = $profile['ownership_percentage'] >= $value;
                    break;
                case 'minority_ownership_min':
                    $verification[$requirement] = $profile['ownership_percentage'] >= $value;
                    break;
                case 'minority_control':
                case 'female_control':
                    // Assume true if ownership % is >50
                    $verification[$requirement] = $profile['ownership_percentage'] >= 51;
                    break;
                case 'independence':
                    $verification[$requirement] = true; // Assume for this example
                    break;
                case 'profit_motivated':
                    $verification[$requirement] = $profile['annual_revenue'] > 0;
                    break;
                case 'us_citizenship':
                    $verification[$requirement] = $documentation['citizenship_docs'] ?? false;
                    break;
                case 'service_connected_disability':
                    $verification[$requirement] = $documentation['disability_docs'] ?? false;
                    break;
            }
        }

        return $verification;
    }

    /**
     * Get personalized support recommendations
     */
    public function getPersonalizedRecommendations(string $profileId): array
    {
        $profile = $this->getBusinessProfile($profileId);
        if (!$profile) {
            throw new \InvalidArgumentException("Profile not found: {$profileId}");
        }

        $recommendations = [];

        // Based on business stage
        switch ($profile['support_level']) {
            case 'beginner':
                $recommendations[] = [
                    'priority' => 'high',
                    'program' => 'training_workshops',
                    'reason' => 'Early-stage business needs foundational skills',
                    'expected_benefit' => 'Improved business fundamentals',
                ];
                $recommendations[] = [
                    'priority' => 'medium',
                    'program' => 'mentorship_network',
                    'reason' => 'Guidance from experienced entrepreneurs',
                    'expected_benefit' => 'Avoid common pitfalls',
                ];
                break;
            case 'intermediate':
                $recommendations[] = [
                    'priority' => 'high',
                    'program' => 'capital_access_fund',
                    'reason' => 'Business is mature enough for expansion funding',
                    'expected_benefit' => 'Growth capital access',
                ];
                $recommendations[] = [
                    'priority' => 'medium',
                    'program' => 'procurement_opportunities',
                    'reason' => 'Ready for larger contracts',
                    'expected_benefit' => 'Stable revenue growth',
                ];
                break;
            case 'advanced':
                $recommendations[] = [
                    'priority' => 'high',
                    'program' => 'procurement_opportunities',
                    'reason' => 'Ready for corporate partnerships',
                    'expected_benefit' => 'Scalable revenue streams',
                ];
                $recommendations[] = [
                    'priority' => 'medium',
                    'program' => 'networking_conferences',
                    'reason' => 'Strategic relationship building',
                    'expected_benefit' => 'High-value connections',
                ];
                break;
        }

        // Based on demographic-specific challenges
        if ($profile['primary_demographic'] === 'women') {
            $recommendations[] = [
                'priority' => 'high',
                'program' => 'legal_advocacy',
                'reason' => 'Addressing potential gender bias in business',
                'expected_benefit' => 'Legal protection and advocacy',
            ];
        }

        if ($profile['primary_demographic'] === 'minority') {
            $recommendations[] = [
                'priority' => 'high',
                'program' => 'procurement_opportunities',
                'reason' => 'Leveraging supplier diversity programs',
                'expected_benefit' => 'Access to corporate contracts',
            ];
        }

        return [
            'profile_id' => $profileId,
            'recommendations' => $recommendations,
            'demographic_insights' => $this->getDemographicInsights($profile['primary_demographic']),
            'next_actions' => $this->getNextRecommendedActions($recommendations),
        ];
    }

    /**
     * Get demographic-specific insights
     */
    private function getDemographicInsights(string $demographic): array
    {
        $insights = [
            'statistics' => [],
            'challenges' => [],
            'opportunities' => [],
            'resources' => [],
        ];

        switch ($demographic) {
            case 'women':
                $insights['statistics'] = [
                    'women_owned_businesses_percentage' => '40% of US businesses',
                    'funding_gap' => 'Women receive 2% of venture capital',
                    'revenue_gap' => 'Women-led businesses earn less on average',
                ];
                $insights['challenges'] = [
                    'funding_discrimination',
                    'networking_barriers',
                    'work_life_balance_stress',
                ];
                $insights['opportunities'] = [
                    'growing_consumer_base',
                    'dedicated_funding_programs',
                    'corporate_diversity_initiatives',
                ];
                break;
            case 'minority':
                $insights['statistics'] = [
                    'minority_owned_businesses_percentage' => '18% of US businesses',
                    'funding_gap' => 'Minority entrepreneurs receive 1% of venture capital',
                    'credit_access' => 'Higher rejection rates for loans',
                ];
                $insights['challenges'] = [
                    'access_to_capital',
                    'networking_exclusion',
                    'bias_in_procurement',
                ];
                $insights['opportunities'] = [
                    'supplier_diversity_programs',
                    'targeted_grant_programs',
                    'growing_minority_consumer_market',
                ];
                break;
            case 'veteran':
                $insights['statistics'] = [
                    'veteran_owned_businesses_percentage' => '9% of US businesses',
                    'success_rate' => 'Veteran-owned businesses have higher success rates',
                ];
                $insights['challenges'] => [
                    'civilian_skills_translation',
                    'business_finance_learning_curve',
                ];
                $insights['opportunities'] => [
                    'government_contracting_preferences',
                    'veteran_business_networks',
                    'leadership_skills_advantage',
                ];
                break;
        }

        return $insights;
    }

    /**
     * Get next recommended actions
     */
    private function getNextRecommendedActions(array $recommendations): array
    {
        $actions = [];
        
        // Prioritize high-priority recommendations
        $highPriority = array_filter($recommendations, function($rec) {
            return $rec['priority'] === 'high';
        });
        
        foreach ($highPriority as $rec) {
            $actions[] = [
                'action' => "Apply to {$rec['program']}",
                'reason' => $rec['reason'],
                'estimated_time' => '1-2 weeks to apply',
                'expected_outcome' => $rec['expected_benefit'],
            ];
        }

        return $actions;
    }

    /**
     * Get success stories and case studies
     */
    public function getSuccessStories(array $filters = []): array
    {
        $stories = [
            [
                'id' => 'story-' . Str::random(6),
                'entrepreneur_name' => 'Maria Rodriguez',
                'business_name' => 'TechSolutions Inc.',
                'demographic' => 'women',
                'industry' => 'technology',
                'program_participated' => 'business_incubator',
                'outcome' => 'Raised $2M Series A funding after incubation',
                'quote' => 'The mentorship and resources helped me scale rapidly.',
                'impact_metrics' => [
                    'jobs_created' => 45,
                    'revenue_growth' => '300%',
                    'funding_raised' => '$2.5M',
                ],
            ],
            [
                'id' => 'story-' . Str::random(6),
                'entrepreneur_name' => 'James Washington',
                'business_name' => 'Heritage Construction',
                'demographic' => 'minority',
                'industry' => 'construction',
                'program_participated' => 'procurement_opportunities',
                'outcome' => 'Secured $5M in municipal contracts',
                'quote' => 'The supplier diversity program opened doors I never imagined.',
                'impact_metrics' => [
                    'contracts_secured' => '$5M',
                    'employees_hired' => 28,
                    'business_expansion' => 'Opened 3 new locations',
                ],
            ],
            [
                'id' => 'story-' . Str::random(6),
                'entrepreneur_name' => 'Sarah Kim',
                'business_name' => 'GreenBeauty Cosmetics',
                'demographic' => 'women',
                'industry' => 'cosmetics',
                'program_participated' => 'capital_access_fund',
                'outcome' => 'Expanded to 15 retail locations',
                'quote' => 'The diverse funding approach helped me reach my target market.',
                'impact_metrics' => [
                    'retail_locations' => 15,
                    'annual_revenue' => '$1.8M',
                    'social_media_followers' => 250000,
                ],
            ],
        ];

        // Apply filters
        if (!empty($filters['demographic'])) {
            $stories = array_filter($stories, function($story) use ($filters) {
                return $story['demographic'] === $filters['demographic'];
            });
        }

        if (!empty($filters['industry'])) {
            $stories = array_filter($stories, function($story) use ($filters) {
                return $story['industry'] === $filters['industry'];
            });
        }

        if (!empty($filters['program_participated'])) {
            $stories = array_filter($stories, function($story) use ($filters) {
                return $story['program_participated'] === $filters['program_participated'];
            });
        }

        return [
            'stories' => array_values($stories),
            'total_count' => count($stories),
            'filters_applied' => $filters,
            'featured' => true,
        ];
    }

    /**
     * Get community support metrics
     */
    public function getCommunitySupportMetrics(array $demographic = null): array
    {
        $metrics = [
            'total_registered_businesses' => mt_rand(500, 5000),
            'funding_distributed' => '$' . mt_rand(10, 100) . 'M',
            'jobs_created' => mt_rand(2000, 20000),
            'average_revenue_growth' => mt_rand(25, 150) . '%',
            'program_participation_rate' => mt_rand(60, 90) . '%',
            'success_rate' => mt_rand(70, 95) . '%',
            'demographic_representation' => [
                'women' => mt_rand(40, 60) . '%',
                'minority' => mt_rand(30, 50) . '%',
                'veteran' => mt_rand(5, 15) . '%',
                'other' => mt_rand(5, 10) . '%',
            ],
        ];

        return [
            'metrics' => $metrics,
            'demographic_breakdown' => $metrics['demographic_representation'],
            'comparison_data' => [
                'national_average_funding' => '$5M',
                'national_avg_revenue_growth' => '35%',
                'national_program_participation' => '45%',
            ],
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get legal resource guides
     */
    public function getLegalResourceGuides(string $category = null): array
    {
        $guides = [
            'starting_your_business' => [
                'title' => 'Starting Your Business: Legal Considerations',
                'description' => 'Legal requirements for starting a business as an underrepresented founder',
                'sections' => [
                    'business_structure_options',
                    'licensing_requirements',
                    'intellectual_property',
                    'employment_law_basics',
                ],
                'target_audience' => ['all'],
                'complexity' => 'beginner',
            ],
            'raising_capital' => [
                'title' => 'Raising Capital: Legal Framework',
                'description' => 'Legal aspects of securing funding for your business',
                'sections' => [
                    'equity_vs_debt_financing',
                    'term_sheet_breakdown',
                    'due_diligence_process',
                    'investor_rights_agreements',
                ],
                'target_audience' => ['minority', 'women'],
                'complexity' => 'intermediate',
            ],
            'protecting_your_business' => [
                'title' => 'Protecting Your Business: Legal Essentials',
                'description' => 'Key legal protections for underrepresented founders',
                'sections' => [
                    'contracts_and_agreements',
                    'liability_protection',
                    'discrimination_protection',
                    'intellectual_property_defense',
                ],
                'target_audience' => ['veteran', 'minority', 'women'],
                'complexity' => 'intermediate',
            ],
        ];

        if ($category && isset($guides[$category])) {
            return [
                'guide' => $guides[$category],
                'category' => $category,
                'message' => 'Legal resource guide retrieved successfully'
            ];
        }

        return [
            'guides' => $category ? [$category => $guides[$category]] : $guides,
            'total_guides' => count($guides),
            'available_categories' => array_keys($guides),
            'message' => 'Legal resource guides retrieved successfully'
        ];
    }
}