<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Get all users for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = User::with(['language', 'roles']);

        // Filter by role
        if ($request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by verification status
        if ($request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'message' => 'Users list for admin management'
        ]);
    }

    /**
     * Update user details.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $adminUser = Auth::user();

        if (!$adminUser || (!$adminUser->hasRole('admin') && $adminUser->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'sometimes|string|max:20',
            'is_verified' => 'sometimes|boolean',
            'language_code' => 'sometimes|string|size:2,3|exists:languages,code',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $updateData = $request->only(['name', 'email', 'phone', 'is_verified', 'language_code']);

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->refresh()),
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Assign or update user roles.
     */
    public function assignRole(Request $request, string $id): JsonResponse
    {
        $adminUser = Auth::user();

        if (!$adminUser || (!$adminUser->hasRole('admin') && $adminUser->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user = User::findOrFail($id);
        $role = Role::where('name', $request->role)->first();

        // Remove all existing roles and assign the new one
        $user->roles()->detach();
        $user->roles()->attach($role->id);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->refresh()),
            'message' => 'User role updated successfully'
        ]);
    }

    /**
     * Delete a user permanently.
     */
    public function destroy(string $id): JsonResponse
    {
        $adminUser = Auth::user();

        if (!$adminUser || (!$adminUser->hasRole('admin') && $adminUser->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::findOrFail($id);

        // Don't allow deletion of admin users
        if ($user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin user'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get user statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::where('is_verified', true)->count(),
            'users_by_role' => Role::withCount('users')->get()->map(function($role) {
                return [
                    'role' => $role->name,
                    'count' => $role->users_count
                ];
            }),
            'users_by_language' => User::selectRaw('language_code, COUNT(*) as count')
                ->groupBy('language_code')
                ->with(['language:id,code,name'])
                ->get()
                ->map(function($item) {
                    return [
                        'language' => $item->language ? $item->language->name : 'Unknown',
                        'code' => $item->language_code,
                        'count' => $item->count
                    ];
                })
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'User statistics'
        ]);
    }
}
