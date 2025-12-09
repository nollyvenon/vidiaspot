<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeatureFlagsController extends Controller
{
    /**
     * Display a listing of feature flags.
     */
    public function index()
    {
        $featureFlags = FeatureFlag::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'feature_flags' => $featureFlags
        ]);
    }

    /**
     * Store a newly created feature flag.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature_key' => 'required|string|unique:feature_flags,feature_key|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_enabled' => 'boolean',
            'allowed_countries' => 'array',
            'allowed_states' => 'array',
            'allowed_cities' => 'array',
            'config' => 'array',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $featureFlag = FeatureFlag::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Feature flag created successfully',
            'feature_flag' => $featureFlag
        ], 201);
    }

    /**
     * Display the specified feature flag.
     */
    public function show($id)
    {
        $featureFlag = FeatureFlag::findOrFail($id);

        return response()->json([
            'success' => true,
            'feature_flag' => $featureFlag
        ]);
    }

    /**
     * Update the specified feature flag.
     */
    public function update(Request $request, $id)
    {
        $featureFlag = FeatureFlag::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'feature_key' => 'required|string|unique:feature_flags,feature_key,' . $id . '|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_enabled' => 'boolean',
            'allowed_countries' => 'array',
            'allowed_states' => 'array',
            'allowed_cities' => 'array',
            'config' => 'array',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $featureFlag->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Feature flag updated successfully',
            'feature_flag' => $featureFlag
        ]);
    }

    /**
     * Remove the specified feature flag.
     */
    public function destroy($id)
    {
        $featureFlag = FeatureFlag::findOrFail($id);
        $featureFlag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature flag deleted successfully'
        ]);
    }

    /**
     * Toggle the active status of a feature flag.
     */
    public function toggleStatus($id)
    {
        $featureFlag = FeatureFlag::findOrFail($id);
        $featureFlag->is_enabled = !$featureFlag->is_enabled;
        $featureFlag->save();

        return response()->json([
            'success' => true,
            'message' => 'Feature flag status updated successfully',
            'feature_flag' => $featureFlag
        ]);
    }

    /**
     * Update regional availability for a feature flag.
     */
    public function updateRegion($id, Request $request)
    {
        $featureFlag = FeatureFlag::findOrFail($id);

        $request->validate([
            'allowed_countries' => 'array',
            'allowed_states' => 'array',
            'allowed_cities' => 'array',
        ]);

        $featureFlag->update([
            'allowed_countries' => $request->allowed_countries,
            'allowed_states' => $request->allowed_states,
            'allowed_cities' => $request->allowed_cities,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feature regional settings updated successfully',
            'feature_flag' => $featureFlag
        ]);
    }

    /**
     * Check if a feature is available for a specific location
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'feature_key' => 'required|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $isAvailable = FeatureFlag::isFeatureAvailable(
            $request->feature_key,
            $request->country,
            $request->state,
            $request->city
        );

        return response()->json([
            'success' => true,
            'is_available' => $isAvailable
        ]);
    }
}
