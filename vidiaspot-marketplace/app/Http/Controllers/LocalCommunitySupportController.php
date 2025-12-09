<?php

namespace App\Http\Controllers;

use App\Services\LocalCommunitySupportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalCommunitySupportController extends Controller
{
    private LocalCommunitySupportService $communityService;

    public function __construct()
    {
        $this->communityService = new LocalCommunitySupportService();
    }

    /**
     * Get available community support initiatives.
     */
    public function getSupportInitiatives()
    {
        $initiatives = $this->communityService->getSupportInitiatives();

        return response()->json([
            'initiatives' => $initiatives,
            'message' => 'Community support initiatives retrieved successfully'
        ]);
    }

    /**
     * Get local businesses near a location.
     */
    public function getLocalBusinesses(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'string',
            'state' => 'string',
            'radius_miles' => 'integer|min:1|max:50',
        ]);

        $location = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'city' => $request->city,
            'state' => $request->state,
        ];

        $radius = $request->radius_miles ?? 10;

        $businesses = $this->communityService->getLocalBusinesses($location, $radius);

        return response()->json([
            'businesses' => $businesses,
            'message' => 'Local businesses retrieved successfully'
        ]);
    }

    /**
     * Get upcoming community events.
     */
    public function getUpcomingCommunityEvents(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'string',
            'state' => 'string',
            'days' => 'integer|min:1|max:365',
        ]);

        $location = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'city' => $request->city,
            'state' => $request->state,
        ];

        $days = $request->days ?? 30;

        $events = $this->communityService->getUpcomingCommunityEvents($location, $days);

        return response()->json([
            'events' => $events,
            'message' => 'Upcoming community events retrieved successfully'
        ]);
    }

    /**
     * Register user for community engagement.
     */
    public function registerForCommunityEngagement(Request $request)
    {
        $request->validate([
            'location' => 'required|array',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
            'location.city' => 'required|string',
            'location.state' => 'required|string',
            'interests' => 'array',
            'interests.*' => 'string',
            'availability' => 'string',
            'volunteer_interest' => 'boolean',
            'local_business_support' => 'boolean',
        ]);

        try {
            $userId = Auth::id();
            $result = $this->communityService->registerForCommunityEngagement($userId, $request->all());

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's community engagement score.
     */
    public function getCommunityEngagementScore()
    {
        $userId = Auth::id();
        $score = $this->communityService->getCommunityEngagementScore($userId);

        return response()->json([
            'engagement_score' => $score,
            'message' => 'Community engagement score retrieved successfully'
        ]);
    }

    /**
     * Get local impact recommendations for the user.
     */
    public function getLocalImpactRecommendations(Request $request)
    {
        $request->validate([
            'location' => 'required|array',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
            'location.city' => 'required|string',
            'location.state' => 'required|string',
        ]);

        $userId = Auth::id();
        $recommendations = $this->communityService->getLocalImpactRecommendations($userId, $request->location);

        return response()->json([
            'recommendations' => $recommendations,
            'message' => 'Local impact recommendations retrieved successfully'
        ]);
    }

    /**
     * Record a community activity for the user.
     */
    public function recordCommunityActivity(Request $request)
    {
        $request->validate([
            'activity_type' => 'required|string|in:local_purchase,local_business_review,community_event_attendance,volunteer_work,resource_sharing,skills_teaching,neighbor_help,local_job_posting,community_feedback',
            'details' => 'array',
            'details.business_id' => 'string',
            'details.event_id' => 'string',
            'details.description' => 'string',
        ]);

        try {
            $userId = Auth::id();
            $result = $this->communityService->recordCommunityActivity($userId, $request->activity_type, $request->details);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get community challenges/competitions.
     */
    public function getCommunityChallenges(Request $request)
    {
        $request->validate([
            'location' => 'required|array',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
            'location.city' => 'required|string',
            'location.state' => 'required|string',
        ]);

        $challenges = $this->communityService->getCommunityChallenges($request->location);

        return response()->json([
            'challenges' => $challenges,
            'message' => 'Community challenges retrieved successfully'
        ]);
    }

    /**
     * Get local economic impact metrics.
     */
    public function getLocalEconomicImpact(Request $request)
    {
        $request->validate([
            'location' => 'required|array',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
            'location.city' => 'required|string',
            'location.state' => 'required|string',
        ]);

        $impact = $this->communityService->getLocalEconomicImpact($request->location);

        return response()->json([
            'economic_impact' => $impact,
            'message' => 'Local economic impact metrics retrieved successfully'
        ]);
    }

    /**
     * Get volunteer opportunities.
     */
    public function getVolunteerOpportunities(Request $request)
    {
        $request->validate([
            'location' => 'required|array',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
            'location.city' => 'required|string',
            'location.state' => 'required|string',
            'limit' => 'integer|min:1|max:50',
        ]);

        $opportunities = $this->communityService->getVolunteerOpportunities($request->location, $request->limit ?? 10);

        return response()->json([
            'opportunities' => $opportunities,
            'message' => 'Volunteer opportunities retrieved successfully'
        ]);
    }

    /**
     * Join a community challenge.
     */
    public function joinCommunityChallenge(Request $request, string $challengeId)
    {
        $userId = Auth::id();
        
        try {
            $result = $this->communityService->joinCommunityChallenge($userId, $challengeId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}