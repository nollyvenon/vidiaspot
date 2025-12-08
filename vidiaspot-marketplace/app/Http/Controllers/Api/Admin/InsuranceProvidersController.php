<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InsuranceProvidersController extends Controller
{
    /**
     * Display a listing of insurance providers.
     */
    public function index()
    {
        $providers = InsuranceProvider::orderBy('name')
                                  ->get();

        return response()->json([
            'success' => true,
            'providers' => $providers
        ]);
    }

    /**
     * Store a newly created insurance provider.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:insurance_providers,name',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'license_number' => 'nullable|string|max:100',
            'categories' => 'required|array',
            'categories.*' => 'string|in:life,health,motor,travel,home,term',
            'coverage_areas' => 'array',
            'features' => 'array',
            'specializations' => 'array',
            'is_active' => 'boolean',
            'rating' => 'numeric|min:0|max:5',
            'claim_settlement_ratio' => 'numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $provider = InsuranceProvider::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Insurance provider created successfully',
            'provider' => $provider
        ], 201);
    }

    /**
     * Display the specified insurance provider.
     */
    public function show($id)
    {
        $provider = InsuranceProvider::findOrFail($id);

        return response()->json([
            'success' => true,
            'provider' => $provider
        ]);
    }

    /**
     * Update the specified insurance provider.
     */
    public function update(Request $request, $id)
    {
        $provider = InsuranceProvider::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:insurance_providers,name,' . $id,
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'license_number' => 'nullable|string|max:100',
            'categories' => 'required|array',
            'categories.*' => 'string|in:life,health,motor,travel,home,term',
            'coverage_areas' => 'array',
            'features' => 'array',
            'specializations' => 'array',
            'is_active' => 'boolean',
            'rating' => 'numeric|min:0|max:5',
            'claim_settlement_ratio' => 'numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $provider->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Insurance provider updated successfully',
            'provider' => $provider
        ]);
    }

    /**
     * Remove the specified insurance provider.
     */
    public function destroy($id)
    {
        $provider = InsuranceProvider::findOrFail($id);

        // Check if any policies are associated with this provider
        if ($provider->policies()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete provider as it has associated policies'
            ], 400);
        }

        $provider->delete();

        return response()->json([
            'success' => true,
            'message' => 'Insurance provider deleted successfully'
        ]);
    }

    /**
     * Toggle the active status of an insurance provider.
     */
    public function toggleStatus($id)
    {
        $provider = InsuranceProvider::findOrFail($id);
        $provider->is_active = !$provider->is_active;
        $provider->save();

        return response()->json([
            'success' => true,
            'message' => 'Provider status updated successfully',
            'provider' => $provider
        ]);
    }
}
