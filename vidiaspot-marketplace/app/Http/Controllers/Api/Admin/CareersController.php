<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CareersController extends Controller
{
    /**
     * Get all careers for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Career::with(['user']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by active status
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by department
        if ($request->department) {
            $query->where('department', $request->department);
        }

        // Filter by location
        if ($request->location) {
            $query->where('location', $request->location);
        }

        // Filter by job type
        if ($request->job_type) {
            $query->where('job_type', $request->job_type);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('requirements', 'like', '%' . $request->search . '%');
            });
        }

        $careers = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $careers,
            'message' => 'Careers list for admin management'
        ]);
    }

    /**
     * Store a new career post.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'job_type' => 'required|in:full_time,part_time,contract,contract_to_hire,internship,freelance',
            'location' => 'required|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'status' => 'in:draft,published,archived',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
            'application_deadline' => 'nullable|date',
            'meta' => 'nullable|array',
        ]);

        $career = Career::create([
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'department' => $request->department,
            'job_type' => $request->job_type,
            'location' => $request->location,
            'salary_range' => $request->salary_range,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'benefits' => $request->benefits,
            'status' => $request->status ?? 'draft',
            'is_active' => $request->is_active ?? false,
            'user_id' => $user->id,
            'published_at' => $request->published_at,
            'application_deadline' => $request->application_deadline,
            'meta' => $request->meta,
        ]);

        return response()->json([
            'success' => true,
            'data' => $career,
            'message' => 'Career post created successfully'
        ], 201);
    }

    /**
     * Update a career post.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'department' => 'sometimes|string|max:100',
            'job_type' => 'sometimes|in:full_time,part_time,contract,contract_to_hire,internship,freelance',
            'location' => 'sometimes|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'description' => 'sometimes|string',
            'requirements' => 'sometimes|string',
            'benefits' => 'nullable|string',
            'status' => 'in:draft,published,archived',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
            'application_deadline' => 'nullable|date',
            'meta' => 'nullable|array',
        ]);

        $career = Career::findOrFail($id);
        $updateData = $request->all();

        if ($request->filled('title')) {
            $updateData['slug'] = \Str::slug($request->title);
        }

        $career->update($updateData);

        return response()->json([
            'success' => true,
            'data' => $career->refresh(),
            'message' => 'Career post updated successfully'
        ]);
    }

    /**
     * Publish a career post.
     */
    public function publish(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $career = Career::findOrFail($id);
        $career->update([
            'status' => 'published',
            'is_active' => true,
            'published_at' => $career->published_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $career->refresh(),
            'message' => 'Career post published successfully'
        ]);
    }

    /**
     * Unpublish a career post.
     */
    public function unpublish(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $career = Career::findOrFail($id);
        $career->update([
            'status' => 'draft',
            'is_active' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $career->refresh(),
            'message' => 'Career post unpublished successfully'
        ]);
    }

    /**
     * Delete a career post.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $career = Career::findOrFail($id);
        $career->delete();

        return response()->json([
            'success' => true,
            'message' => 'Career post deleted successfully'
        ]);
    }
}
