<?php

namespace App\Http\Controllers;

use App\Services\LocalArtisansAndSmallBusinessesSupportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalArtisansAndSmallBusinessesSupportController extends Controller
{
    private LocalArtisansAndSmallBusinessesSupportService $artisansService;

    public function __construct()
    {
        $this->artisansService = new LocalArtisansAndSmallBusinessesSupportService();
    }

    /**
     * Get available support types.
     */
    public function getSupportTypes()
    {
        $supportTypes = $this->artisansService->getSupportTypes();

        return response()->json([
            'support_types' => $supportTypes,
            'message' => 'Support types retrieved successfully'
        ]);
    }

    /**
     * Get artisan categories.
     */
    public function getArtisanCategories()
    {
        $categories = $this->artisansService->getArtisanCategories();

        return response()->json([
            'categories' => $categories,
            'message' => 'Artisan categories retrieved successfully'
        ]);
    }

    /**
     * Get certification levels.
     */
    public function getCertificationLevels()
    {
        $levels = $this->artisansService->getCertificationLevels();

        return response()->json([
            'certification_levels' => $levels,
            'message' => 'Certification levels retrieved successfully'
        ]);
    }

    /**
     * Register a business.
     */
    public function registerBusiness(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'sub_category' => 'required|string',
            'owner_name' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'physical_address' => 'required|array',
            'physical_address.street' => 'required|string',
            'physical_address.city' => 'required|string',
            'physical_address.state' => 'required|string',
            'physical_address.zip' => 'required|string',
            'years_operating' => 'required|integer|min:0|max:100',
            'local_hiring_percentage' => 'integer|min:0|max:100',
            'sustainable_practices' => 'array',
            'products' => 'array',
            'gallery' => 'array',
        ]);

        try {
            $userId = Auth::id();
            $result = $this->artisansService->registerBusiness($request->all(), $userId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get a business profile.
     */
    public function getBusinessProfile(Request $request, string $businessId)
    {
        $profile = $this->artisansService->getBusinessProfile($businessId);

        if (!$profile) {
            return response()->json([
                'error' => 'Business not found'
            ], 404);
        }

        return response()->json([
            'business_profile' => $profile,
            'message' => 'Business profile retrieved successfully'
        ]);
    }

    /**
     * Get businesses by category.
     */
    public function getBusinessesByCategory(Request $request, string $category)
    {
        $request->validate([
            'sub_category' => 'string',
            'min_rating' => 'numeric|min:0|max:5',
            'certification_level' => 'string',
        ]);

        $filters = $request->only(['sub_category', 'min_rating', 'certification_level']);

        $businesses = $this->artisansService->getBusinessesByCategory($category, $filters);

        return response()->json([
            'businesses' => $businesses,
            'message' => 'Businesses by category retrieved successfully'
        ]);
    }

    /**
     * Find artisans near user's location.
     */
    public function findArtisansNearbyLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'string',
            'state' => 'string',
            'category' => 'string',
            'radius_miles' => 'integer|min:1|max:100',
        ]);

        $location = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'city' => $request->city,
            'state' => $request->state,
        ];

        $radius = $request->radius_miles ?? 15;

        $artisans = $this->artisansService->findArtisansNearbyLocation($location, $request->category, $radius);

        return response()->json([
            'artisans' => $artisans,
            'message' => 'Artisans near location retrieved successfully'
        ]);
    }

    /**
     * Apply for business certification.
     */
    public function applyForCertification(Request $request, string $businessId)
    {
        $request->validate([
            'certification_level' => 'required|string',
            'documentation' => 'required|array',
            'documentation.business_license' => 'string',
            'documentation.tax_id' => 'string',
            'documentation.insurance_certificates' => 'array',
            'documentation.sustainability_practices' => 'string',
        ]);

        try {
            $result = $this->artisansService->applyForCertification($businessId, $request->certification_level, $request->documentation);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get eligible support programs for a business.
     */
    public function getEligibleSupportPrograms(Request $request, string $businessId)
    {
        try {
            $programs = $this->artisansService->getEligibleSupportPrograms($businessId);

            return response()->json([
                'programs' => $programs,
                'message' => 'Eligible support programs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Apply for a support program.
     */
    public function applyForSupportProgram(Request $request, string $businessId)
    {
        $request->validate([
            'program_id' => 'required|string',
            'application_data' => 'required|array',
        ]);

        try {
            $result = $this->artisansService->applyForSupportProgram($businessId, $request->program_id, $request->application_data);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get business dashboard metrics.
     */
    public function getBusinessDashboardMetrics(Request $request, string $businessId)
    {
        try {
            $metrics = $this->artisansService->getBusinessDashboardMetrics($businessId);

            return response()->json([
                'metrics' => $metrics,
                'message' => 'Business dashboard metrics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get artisan directory with filters.
     */
    public function getArtisanDirectory(Request $request)
    {
        $request->validate([
            'search' => 'string',
            'min_sustainability_score' => 'integer|min:0|max:100',
            'local_hiring_preference' => 'integer|min:0|max:100',
            'category' => 'string',
            'certification_level' => 'string',
        ]);

        $filters = $request->only([
            'search',
            'min_sustainability_score',
            'local_hiring_preference',
            'category',
            'certification_level',
        ]);

        $directory = $this->artisansService->getArtisanDirectory($filters);

        return response()->json([
            'directory' => $directory,
            'message' => 'Artisan directory retrieved successfully'
        ]);
    }

    /**
     * Get resource center content.
     */
    public function getResourceCenterContent(Request $request)
    {
        $request->validate([
            'category' => 'string',
            'difficulty' => 'string',
        ]);

        $filters = $request->only(['category', 'difficulty']);

        $content = $this->artisansService->getResourceCenterContent($filters);

        return response()->json([
            'content' => $content,
            'message' => 'Resource center content retrieved successfully'
        ]);
    }

    /**
     * Get community impact metrics for local businesses.
     */
    public function getCommunityImpactMetrics(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required|string',
            'state' => 'required|string',
        ]);

        $location = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'city' => $request->city,
            'state' => $request->state,
        ];

        $filters = $request->all();

        $metrics = $this->artisansService->getCommunityImpactMetrics($location, $filters);

        return response()->json([
            'metrics' => $metrics,
            'message' => 'Community impact metrics retrieved successfully'
        ]);
    }
}