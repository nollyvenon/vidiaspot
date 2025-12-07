<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get chat history between two users.
     */
    public function getChatHistory(Request $request, $userId): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $otherUser = User::findOrFail($userId);

        $chats = Chat::betweenUsers($authenticatedUser->id, $otherUser->id)
            ->with(['sender', 'receiver'])
            ->paginate(50);

        // Mark messages as read
        Chat::markAsRead($authenticatedUser->id, $otherUser->id);

        return response()->json([
            'chats' => $chats,
            'other_user' => $otherUser,
        ]);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'messageable_type' => 'nullable|string',
            'messageable_id' => 'nullable|integer',
        ]);

        $chat = Chat::create([
            'sender_id' => $authenticatedUser->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'messageable_type' => $request->messageable_type,
            'messageable_id' => $request->messageable_id,
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'chat' => $chat->load(['sender', 'receiver']),
        ], 201);
    }

    /**
     * Get all users that the authenticated user has chatted with.
     */
    public function getChatUsers(): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userIds = Chat::selectRaw('CASE 
                WHEN sender_id = ? THEN receiver_id 
                WHEN receiver_id = ? THEN sender_id 
            END as user_id', [$authenticatedUser->id, $authenticatedUser->id])
            ->where(function($query) use ($authenticatedUser) {
                $query->where('sender_id', $authenticatedUser->id)
                      ->orWhere('receiver_id', $authenticatedUser->id);
            })
            ->groupBy('user_id')
            ->pluck('user_id');

        $users = User::whereIn('id', $userIds)
            ->with(['chatsReceived' => function($query) use ($authenticatedUser) {
                $query->where('sender_id', '!=', $authenticatedUser->id)
                      ->where('is_read', false)
                      ->count();
            }])
            ->get();

        // Add unread count for each user
        $users = $users->map(function($user) use ($authenticatedUser) {
            $user->unread_count = Chat::where('sender_id', '!=', $authenticatedUser->id)
                ->where('receiver_id', $authenticatedUser->id)
                ->where('sender_id', $user->id)
                ->where('is_read', false)
                ->count();
            return $user;
        });

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Get user's chat history.
     */
    public function getUserChatHistory(Request $request): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Chat::where(function($query) use ($authenticatedUser) {
            $query->where('sender_id', $authenticatedUser->id)
                  ->orWhere('receiver_id', $authenticatedUser->id);
        })
        ->with(['sender', 'receiver']);

        if ($request->filled('partner_id')) {
            $query->where(function($q) use ($authenticatedUser, $request) {
                $q->where(function($sub) use ($authenticatedUser, $request) {
                    $sub->where('sender_id', $authenticatedUser->id)
                        ->where('receiver_id', $request->partner_id);
                })
                ->orWhere(function($sub) use ($authenticatedUser, $request) {
                    $sub->where('sender_id', $request->partner_id)
                        ->where('receiver_id', $authenticatedUser->id);
                });
            });
        }

        $chats = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'chats' => $chats,
        ]);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request, $userId = null): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Chat::where('receiver_id', $authenticatedUser->id)->where('is_read', false);

        if ($userId) {
            $query->where('sender_id', $userId);
        }

        $query->update(['is_read' => true]);

        return response()->json([
            'message' => 'Messages marked as read',
        ]);
    }

    /**
     * Get unread message count.
     */
    public function getUnreadCount(): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $unreadCount = Chat::where('receiver_id', $authenticatedUser->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }
}