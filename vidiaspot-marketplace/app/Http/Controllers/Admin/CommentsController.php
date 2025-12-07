<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CommentsController extends Controller
{
    /**
     * Display comments management page.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Comment::with(['user', 'commentable']);

        if ($request->filled('search')) {
            $query->where('content', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('private')) {
            $isPrivate = $request->private === 'yes';
            $query->where('is_private', $isPrivate);
        }

        if ($request->filled('approved')) {
            $isApproved = $request->approved === 'yes';
            $query->where('is_approved', $isApproved);
        }

        if ($request->filled('commentable_type')) {
            $query->where('commentable_type', $request->commentable_type);
        }

        $comments = $query->latest()->paginate(25);

        $commentableTypes = [
            'App\Models\Ad' => 'Ad',
            'App\Models\Vendor' => 'Vendor',
            'App\Models\Payment' => 'Payment',
            'App\Models\Subscription' => 'Subscription',
            'App\Models\Blog' => 'Blog',
            'App\Models\Category' => 'Category',
        ];

        return $this->adminView('admin.comments.index', [
            'comments' => $comments,
            'commentableTypes' => $commentableTypes,
        ]);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'content' => 'required|string',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'is_private' => 'boolean',
        ]);

        $comment = Comment::create([
            'commentable_type' => $request->commentable_type,
            'commentable_id' => $request->commentable_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'is_private' => $request->is_private ?? false,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load(['user', 'replies']),
        ], 201);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'content' => 'required|string',
            'is_private' => 'boolean',
            'is_approved' => 'boolean',
        ]);

        $comment->update([
            'content' => $request->content,
            'is_private' => $request->is_private ?? $comment->is_private,
            'is_approved' => $request->is_approved ?? $comment->is_approved,
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment->refresh(),
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->checkAdminAccess();

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }

    /**
     * Get comments for a specific entity.
     */
    public function getEntityComments(Request $request, string $entityType, int $entityId): JsonResponse
    {
        $this->checkAdminAccess();

        $comments = Comment::with(['user', 'replies.user'])
            ->where([
                'commentable_type' => $entityType,
                'commentable_id' => $entityId,
            ])
            ->whereNull('parent_id') // Only top-level comments
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments,
        ]);
    }

    /**
     * Get chat for a specific user.
     */
    public function getUserChat(Request $request, int $userId): JsonResponse
    {
        $this->checkAdminAccess();

        $comments = Comment::with(['user'])
            ->where(function($query) use ($userId) {
                $query->where('commentable_type', 'App\Models\User')
                      ->where('commentable_id', $userId);
            })
            ->where('is_private', true) // Only admin notes
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments,
        ]);
    }
}