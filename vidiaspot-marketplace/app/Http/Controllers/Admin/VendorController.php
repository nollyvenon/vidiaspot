<?php

namespace App\Http\Controllers\Admin;

use App\Models\Vendor;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Vendor::with(['user', 'country', 'state', 'city']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('verified')) {
            $isVerified = $request->verified === 'yes';
            $query->where('is_verified', $isVerified);
        }

        if ($request->filled('featured')) {
            $isFeatured = $request->featured === 'yes';
            $query->where('is_featured', $isFeatured);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $vendors = $query->latest()->paginate(25);

        return $this->adminView('admin.vendors.index', [
            'vendors' => $vendors,
        ]);
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor): View
    {
        $this->checkAdminAccess();

        $vendor->load(['user', 'country', 'state', 'city', 'ads', 'featuredAds']);

        return $this->adminView('admin.vendors.show', [
            'vendor' => $vendor,
        ]);
    }

    /**
     * Approve the specified vendor.
     */
    public function approve(Vendor $vendor): JsonResponse
    {
        $this->checkAdminAccess();

        $vendor->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Also verify the vendor
        $vendor->update([
            'is_verified' => true,
        ]);

        return response()->json([
            'message' => 'Vendor approved successfully',
            'vendor' => $vendor->refresh(),
        ]);
    }

    /**
     * Reject the specified vendor.
     */
    public function reject(Request $request, Vendor $vendor): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $vendor->update([
            'status' => 'rejected',
            'is_verified' => false,
        ]);

        return response()->json([
            'message' => 'Vendor rejected successfully',
            'vendor' => $vendor->refresh(),
        ]);
    }

    /**
     * Suspend the specified vendor.
     */
    public function suspend(Request $request, Vendor $vendor): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'suspension_reason' => 'required|string|max:500',
        ]);

        $vendor->update([
            'status' => 'suspended',
        ]);

        return response()->json([
            'message' => 'Vendor suspended successfully',
            'vendor' => $vendor->refresh(),
        ]);
    }

    /**
     * Toggle featured status for the specified vendor.
     */
    public function toggleFeatured(Request $request, Vendor $vendor): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'featured' => 'required|boolean',
        ]);

        $vendor->update([
            'is_featured' => $request->featured,
        ]);

        return response()->json([
            'message' => 'Vendor featured status updated successfully',
            'vendor' => $vendor->refresh(),
        ]);
    }

    /**
     * Get vendor statistics for admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $this->checkAdminAccess();

        $totalVendors = Vendor::count();
        $approvedVendors = Vendor::where('status', 'approved')->count();
        $pendingVendors = Vendor::where('status', 'pending')->count();
        $rejectedVendors = Vendor::where('status', 'rejected')->count();
        $suspendedVendors = Vendor::where('status', 'suspended')->count();

        return response()->json([
            'total_vendors' => $totalVendors,
            'approved_vendors' => $approvedVendors,
            'pending_vendors' => $pendingVendors,
            'rejected_vendors' => $rejectedVendors,
            'suspended_vendors' => $suspendedVendors,
        ]);
    }
}