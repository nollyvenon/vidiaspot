<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RiskManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RiskManagementController extends Controller
{
    protected RiskManagementService $riskService;

    public function __construct(RiskManagementService $riskService)
    {
        $this->riskService = $riskService;
    }

    /**
     * Calculate portfolio risk
     */
    public function calculatePortfolioRisk(Request $request): JsonResponse
    {
        $data = $this->riskService->calculatePortfolioRisk(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Portfolio risk calculated successfully'
        ]);
    }

    /**
     * Calculate diversification analyzer
     */
    public function calculateDiversification(Request $request): JsonResponse
    {
        $data = $this->riskService->calculateDiversification(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Diversification analysis completed successfully'
        ]);
    }

    /**
     * Calculate volatility indicators
     */
    public function calculateVolatilityIndicators(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id',
            'days' => 'nullable|integer|min=7|max=365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->riskService->calculateVolatilityIndicators(
            $request->category_id,
            $request->days ?? 30
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Volatility indicators calculated successfully'
        ]);
    }

    /**
     * Calculate risk/reward ratio
     */
    public function calculateRiskRewardRatio(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'potential_gain' => 'required|numeric',
            'potential_loss' => 'required|numeric',
            'probability_of_gain' => 'nullable|numeric|min:0|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->riskService->calculateRiskRewardRatio(
            $request->potential_gain,
            $request->potential_loss,
            $request->probability_of_gain ?? 0.5
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Risk/reward ratio calculated successfully'
        ]);
    }

    /**
     * Calculate position sizing
     */
    public function calculatePositionSize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_size' => 'required|numeric',
            'risk_percentage' => 'required|numeric|min:0|max:100',
            'entry_price' => 'required|numeric',
            'stop_loss_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->riskService->calculatePositionSize(
            $request->account_size,
            $request->risk_percentage,
            $request->entry_price,
            $request->stop_loss_price
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Position size calculated successfully'
        ]);
    }

    /**
     * Calculate drawdown analysis
     */
    public function calculateDrawdownAnalysis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days' => 'nullable|integer|min=7|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->riskService->calculateDrawdownAnalysis(
            auth()->id(),
            $request->days ?? 90
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Drawdown analysis completed successfully'
        ]);
    }

    /**
     * Calculate performance attribution
     */
    public function calculatePerformanceAttribution(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->riskService->calculatePerformanceAttribution(
            auth()->id(),
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Performance attribution calculated successfully'
        ]);
    }
}