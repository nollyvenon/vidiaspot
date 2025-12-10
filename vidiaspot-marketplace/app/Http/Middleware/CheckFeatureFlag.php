<?php

namespace App\Http\Middleware;

use App\Services\FeatureFlagService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckFeatureFlag
{
    protected $featureFlagService;

    public function __construct(FeatureFlagService $featureFlagService)
    {
        $this->featureFlagService = $featureFlagService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $featureKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        // Get location from request or other sources
        $country = $request->input('country') ?? 
                  $request->header('X-Country') ?? 
                  session('country') ?? 
                  null;
                  
        $state = $request->input('state') ?? 
                $request->header('X-State') ?? 
                session('state') ?? 
                null;
                
        $city = $request->input('city') ?? 
               $request->header('X-City') ?? 
               session('city') ?? 
               null;

        // Check if the feature is available
        if (!$this->featureFlagService->isFeatureAvailable($featureKey, $country, $state, $city)) {
            return response()->json([
                'success' => false,
                'message' => 'This feature is not currently available in your region or has been disabled.',
            ], 403);
        }

        // Additional check: verify user has access to this feature
        if (!Auth::check() || !$this->featureFlagService->userHasAccessToFeature($featureKey)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this feature.',
            ], 403);
        }

        return $next($request);
    }
}