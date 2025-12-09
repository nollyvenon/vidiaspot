<?php

namespace App\Http\Controllers;

use App\Services\RecyclingProgramIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecyclingProgramIntegrationController extends Controller
{
    private RecyclingProgramIntegrationService $recyclingService;

    public function __construct()
    {
        $this->recyclingService = new RecyclingProgramIntegrationService();
    }

    /**
     * Get available recycling programs.
     */
    public function getRecyclingPrograms()
    {
        $programs = $this->recyclingService->getRecyclingPrograms();

        return response()->json([
            'programs' => $programs,
            'message' => 'Recycling programs retrieved successfully'
        ]);
    }

    /**
     * Get material recycling information.
     */
    public function getMaterialRecyclingInfo()
    {
        $materials = $this->recyclingService->getMaterialRecyclingInfo();

        return response()->json([
            'materials' => $materials,
            'message' => 'Material recycling information retrieved successfully'
        ]);
    }

    /**
     * Find recycling programs for a specific material.
     */
    public function findProgramsForMaterial(Request $request, string $material)
    {
        $request->validate([
            'location' => 'string',
        ]);

        $result = $this->recyclingService->findProgramsForMaterial($material, $request->location);

        return response()->json([
            'result' => $result,
            'message' => 'Recycling programs for material retrieved successfully'
        ]);
    }

    /**
     * Find nearby collection points for a program and location.
     */
    public function findNearbyCollectionPoints(Request $request)
    {
        $request->validate([
            'program_id' => 'required|string',
            'location' => 'required|string',
        ]);

        $points = $this->recyclingService->findNearbyCollectionPoints($request->program_id, $request->location);

        return response()->json([
            'collection_points' => $points,
            'program_id' => $request->program_id,
            'location' => $request->location,
            'message' => 'Nearby collection points retrieved successfully'
        ]);
    }

    /**
     * Schedule a recycling pickup.
     */
    public function schedulePickup(Request $request)
    {
        $request->validate([
            'program_id' => 'required|string',
            'materials' => 'required|array',
            'materials.*' => 'required|string',
            'address' => 'required|array',
            'address.street' => 'required|string',
            'address.city' => 'required|string',
            'address.state' => 'required|string',
            'address.zip' => 'required|string',
            'contact_name' => 'required|string',
            'contact_phone' => 'required|string',
            'pickup_date' => 'date',
            'estimated_weight' => 'numeric',
            'special_instructions' => 'string',
        ]);

        try {
            $result = $this->recyclingService->schedulePickup($request->all());

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get program credentials status.
     */
    public function getProgramCredentialsStatus(Request $request, string $programId)
    {
        $status = $this->recyclingService->getProgramCredentialsStatus($programId);

        return response()->json([
            'status' => $status,
            'program_id' => $programId,
            'message' => 'Program credentials status retrieved successfully'
        ]);
    }

    /**
     * Register with a recycling program.
     */
    public function registerWithProgram(Request $request, string $programId)
    {
        $request->validate([
            'api_key' => 'required|string',
            'account_id' => 'required|string',
            'company_name' => 'required|string',
            'company_address' => 'required|array',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
        ]);

        $credentials = [
            'api_key' => $request->api_key,
            'account_id' => $request->account_id,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
        ];

        $result = $this->recyclingService->registerWithProgram($programId, $credentials);

        return response()->json($result);
    }

    /**
     * Get recycling statistics for materials.
     */
    public function getRecyclingStatistics(Request $request)
    {
        $request->validate([
            'materials' => 'required|array',
            'materials.*' => 'required|string',
        ]);

        $stats = $this->recyclingService->getRecyclingStatistics($request->materials);

        return response()->json([
            'statistics' => $stats,
            'message' => 'Recycling statistics retrieved successfully'
        ]);
    }

    /**
     * Calculate environmental impact of recycling.
     */
    public function calculateRecyclingImpact(Request $request)
    {
        $request->validate([
            'material_weights' => 'required|array',
            'material_weights.*' => 'required|numeric|min:0',
        ]);

        $impact = $this->recyclingService->calculateRecyclingImpact($request->material_weights);

        return response()->json([
            'impact' => $impact,
            'message' => 'Recycling impact calculated successfully'
        ]);
    }

    /**
     * Get program-specific information.
     */
    public function getProgramInfo(Request $request, string $programId)
    {
        $info = $this->recyclingService->getProgramInfo($programId);

        if (!$info) {
            return response()->json([
                'error' => 'Program not found'
            ], 404);
        }

        return response()->json([
            'program' => $info,
            'message' => 'Program information retrieved successfully'
        ]);
    }

    /**
     * Generate a recycling report for the current user.
     */
    public function generateRecyclingReport(Request $request)
    {
        $request->validate([
            'recycling_activities' => 'required|array',
            'recycling_activities.*.material' => 'required|string',
            'recycling_activities.*.weight' => 'required|numeric|min:0',
            'recycling_activities.*.program' => 'required|string',
        ]);

        $userId = Auth::id();
        $report = $this->recyclingService->generateRecyclingReport($userId, $request->recycling_activities);

        return response()->json([
            'report' => $report,
            'message' => 'Recycling report generated successfully'
        ]);
    }
}