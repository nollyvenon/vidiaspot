<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VendorsController extends Controller
{
    /**
     * Get all vendors for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Vendor::with(['user', 'country', 'state', 'city']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by verification status
        if ($request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified);
        }

        // Filter by featured status
        if ($request->is_featured !== null) {
            $query->where('is_featured', $request->is_featured);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('business_name', 'like', '%' . $request->search . '%')
                  ->orWhere('business_email', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $vendors = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $vendors,
            'message' => 'Vendors list for admin management'
        ]);
    }

    /**
     * Update vendor details.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'business_email' => 'sometimes|string|email|max:255',
            'business_phone' => 'sometimes|string|max:20',
            'business_description' => 'nullable|string',
            'business_type' => 'nullable|string|max:100',
            'business_registration_number' => 'nullable|string|max:255',
            'logo_url' => 'nullable|url',
            'banner_url' => 'nullable|url',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'state_id' => 'sometimes|integer|exists:states,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
            'address' => 'nullable|string',
            'is_verified' => 'boolean',
            'rating' => 'sometimes|numeric|min:0|max:5',
            'is_featured' => 'boolean',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $vendor->refresh(),
            'message' => 'Vendor updated successfully'
        ]);
    }

    /**
     * Approve a vendor.
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::findOrFail($id);
        $vendor->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
            'is_verified' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $vendor->refresh(),
            'message' => 'Vendor approved successfully'
        ]);
    }

    /**
     * Reject a vendor.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $vendor->refresh(),
            'message' => 'Vendor rejected successfully'
        ]);
    }

    /**
     * Suspend a vendor.
     */
    public function suspend(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'suspended']);

        return response()->json([
            'success' => true,
            'data' => $vendor->refresh(),
            'message' => 'Vendor suspended successfully'
        ]);
    }

    /**
     * Get vendor statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_vendors' => Vendor::count(),
            'approved_vendors' => Vendor::where('status', 'approved')->count(),
            'pending_vendors' => Vendor::where('status', 'pending')->count(),
            'suspended_vendors' => Vendor::where('status', 'suspended')->count(),
            'verified_vendors' => Vendor::where('is_verified', true)->count(),
            'featured_vendors' => Vendor::where('is_featured', true)->count(),
            'vendors_by_country' => Vendor::selectRaw('country_id, COUNT(*) as count')
                ->groupBy('country_id')
                ->with(['country:id,name'])
                ->get()
                ->map(function($item) {
                    return [
                        'country' => $item->country ? $item->country->name : 'Unknown',
                        'count' => $item->count
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Vendor statistics'
        ]);
    }

    /**
     * Toggle vendor verification status.
     */
    public function toggleVerification(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::findOrFail($id);
        $vendor->update(['is_verified' => !$vendor->is_verified]);

        return response()->json([
            'success' => true,
            'data' => $vendor->refresh(),
            'message' => 'Vendor verification status updated successfully'
        ]);
    }

    /**
     * Toggle vendor featured status.
     */
    public function toggleFeatured(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::findOrFail($id);
        $vendor->update(['is_featured' => !$vendor->is_featured]);

        return response()->json([
            'success' => true,
            'data' => $vendor->refresh(),
            'message' => 'Vendor featured status updated successfully'
        ]);
    }
}
