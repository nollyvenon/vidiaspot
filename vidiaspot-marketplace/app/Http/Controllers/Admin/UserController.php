<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Vendor;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = User::with(['roles', 'vendor']);

        // Apply filters
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('verified')) {
            $isVerified = $request->verified === 'yes';
            $query->where('is_verified', $isVerified);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(25);

        $roles = Role::all();

        return $this->adminView('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $this->checkAdminAccess();

        $user->load(['roles', 'vendor', 'ads', 'payments', 'subscriptions', 'blogs']);

        return $this->adminView('admin.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Update user verification status.
     */
    public function updateVerification(Request $request, User $user): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'is_verified' => 'required|boolean',
        ]);

        $user->update([
            'is_verified' => $request->is_verified,
        ]);

        return response()->json([
            'message' => 'User verification status updated successfully',
            'user' => $user->refresh(),
        ]);
    }

    /**
     * Update user role.
     */
    public function updateUserRole(Request $request, User $user): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // Remove all existing roles
        $user->roles()->detach();

        // Assign the new role
        $user->assignRole($request->role);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user->refresh(),
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user): JsonResponse
    {
        $this->checkAdminAccess();

        $user->update([
            'is_verified' => !$user->is_verified,
        ]);

        return response()->json([
            'message' => 'User active status toggled successfully',
            'user' => $user->refresh(),
        ]);
    }

    /**
     * Get user statistics for admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $this->checkAdminAccess();

        $totalUsers = User::count();
        $verifiedUsers = User::where('is_verified', true)->count();
        $adminUsers = User::admin()->count();
        $sellerUsers = User::seller()->count();
        $regularUsers = User::normalUser()->count();

        return response()->json([
            'total_users' => $totalUsers,
            'verified_users' => $verifiedUsers,
            'admin_users' => $adminUsers,
            'seller_users' => $sellerUsers,
            'regular_users' => $regularUsers,
        ]);
    }
}