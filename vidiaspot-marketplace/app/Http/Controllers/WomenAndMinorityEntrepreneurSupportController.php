<?php

namespace App\Http\Controllers;

use App\Services\WomenAndMinorityEntrepreneurSupportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WomenAndMinorityEntrepreneurSupportController extends Controller
{
    private WomenAndMinorityEntrepreneurSupportService $wmepService;

    public function __construct()
    {
        $this->wmepService = new WomenAndMinorityEntrepreneurSupportService();
    }

    /**
     * Get available support programs.
     */
    public function getSupportPrograms()
    {
        $programs = $this->wmepService->getSupportPrograms();

        return response()->json([
            'programs' => $programs,
            'message' => 'Support programs retrieved successfully'
        ]);
    }

    /**
     * Get certification requirements.
     */
    public function getCertificationRequirements()
    {
        $certifications = $this->wmepService->getCertificationRequirements();

        return response()->json([
            'certifications' => $certifications,
            'message' => 'Certification requirements retrieved successfully'
        ]);
    }

    /**
     * Get specialized resources.
     */
    public function getResources()
    {
        $resources = $this->wmepService->getResources();

        return response()->json([
            'resources' => $resources,
            'message' => 'Specialized resources retrieved successfully'
        ]);
    }

    /**
     * Register for support programs.
     */
    public function registerForSupport(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'demographic_identity' => 'required|string|in:women,minority,veteran,disabled,lgbtq',
            'business_type' => 'required|string',
            'years_operating' => 'required|integer|min:0',
            'annual_revenue' => 'required|numeric|min:0',
            'ownership_percentage' => 'required|integer|min:51|max:100',
            'contact_email' => 'required|email',
            'physical_address' => 'required|array',
            'physical_address.street' => 'required|string',
            'physical_address.city' => 'required|string',
            'physical_address.state' => 'required|string',
            'physical_address.zip' => 'required|string',
        ]);

        try {
            $userId = Auth::id();
            $result = $this->wmepService->registerForSupport($request->all(), $userId, $request->demographic_identity);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Apply for support program.
     */
    public function applyForSupportProgram(Request $request, string $profileId)
    {
        $request->validate([
            'program_id' => 'required|string',
            'application_data' => 'required|array',
        ]);

        try {
            $result = $this->wmepService->applyForSupportProgram($profileId, $request->program_id, $request->application_data);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get available certifications.
     */
    public function getAvailableCertifications(Request $request)
    {
        $request->validate([
            'demographic' => 'string|in:women,minority,veteran,all',
        ]);

        $certifications = $this->wmepService->getAvailableCertifications($request->demographic);

        return response()->json([
            'certifications' => $certifications,
            'message' => 'Available certifications retrieved successfully'
        ]);
    }

    /**
     * Apply for business certification.
     */
    public function applyForCertification(Request $request, string $profileId)
    {
        $request->validate([
            'certification_type' => 'required|string',
            'documentation' => 'required|array',
        ]);

        try {
            $result = $this->wmepService->applyForCertification($profileId, $request->certification_type, $request->documentation);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get personalized recommendations for a business profile.
     */
    public function getPersonalizedRecommendations(Request $request, string $profileId)
    {
        try {
            $recommendations = $this->wmepService->getPersonalizedRecommendations($profileId);

            return response()->json([
                'recommendations' => $recommendations,
                'message' => 'Personalized recommendations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get success stories.
     */
    public function getSuccessStories(Request $request)
    {
        $request->validate([
            'demographic' => 'string|in:women,minority,veteran',
            'industry' => 'string',
            'program_participated' => 'string',
        ]);

        $filters = $request->only(['demographic', 'industry', 'program_participated']);

        $stories = $this->wmepService->getSuccessStories($filters);

        return response()->json([
            'success_stories' => $stories,
            'message' => 'Success stories retrieved successfully'
        ]);
    }

    /**
     * Get community support metrics.
     */
    public function getCommunitySupportMetrics(Request $request)
    {
        $request->validate([
            'demographic' => 'string|in:women,minority,veteran',
        ]);

        $metrics = $this->wmepService->getCommunitySupportMetrics($request->demographic);

        return response()->json([
            'metrics' => $metrics,
            'message' => 'Community support metrics retrieved successfully'
        ]);
    }

    /**
     * Get legal resource guides.
     */
    public function getLegalResourceGuides(Request $request)
    {
        $request->validate([
            'category' => 'string|in:starting_your_business,raising_capital,protecting_your_business',
        ]);

        $guides = $this->wmepService->getLegalResourceGuides($request->category);

        return response()->json([
            'guides' => $guides,
            'message' => 'Legal resource guides retrieved successfully'
        ]);
    }
}