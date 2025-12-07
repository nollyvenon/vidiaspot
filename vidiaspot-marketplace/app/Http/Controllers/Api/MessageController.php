<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $query = Message::with(['sender', 'receiver', 'ad'])
            ->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            });

        // Filter by conversation partner
        if ($request->partner_id) {
            $query->where(function($q) use ($userId, $request) {
                $q->where('sender_id', $request->partner_id)
                  ->where('receiver_id', $userId)
                  ->orWhere(function($q) use ($userId, $request) {
                      $q->where('sender_id', $userId)
                        ->where('receiver_id', $request->partner_id);
                  });
            });
        }

        // Filter by ad
        if ($request->ad_id) {
            $query->where('ad_id', $request->ad_id);
        }

        // Filter by read status
        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        $query->orderBy('created_at', 'desc');

        $messages = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\MessageResource::collection($messages)
        ]);
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'ad_id' => 'nullable|exists:ads,id',
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'ad_id' => $request->ad_id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\MessageResource($message->load(['sender', 'receiver', 'ad']))
        ], 201);
    }

    /**
     * Display the specified message.
     */
    public function show(string $id): JsonResponse
    {
        $message = Message::with(['sender', 'receiver', 'ad'])->findOrFail($id);

        // Check if user is the sender or receiver
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Mark as read if received by the receiver
        if ($message->receiver_id === Auth::id() && !$message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\MessageResource($message)
        ]);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $message = Message::findOrFail($id);

        // Check if user is the receiver
        if ($message->receiver_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $message->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\MessageResource($message)
        ]);
    }

    /**
     * Get conversations for the authenticated user.
     */
    public function conversations(): JsonResponse
    {
        $userId = Auth::id();

        // Get all message IDs where the user is either sender or receiver
        $messageIds = Message::where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->pluck('id');

        // Get the latest message for each conversation partner
        $conversations = Message::with(['sender', 'receiver', 'ad'])
            ->whereIn('id', $messageIds)
            ->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($message) use ($userId) {
                // Group by conversation partner (the other user in the conversation)
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            });

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\MessageResource::collection($conversations)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $message = Message::findOrFail($id);

        // Check if user is the sender or receiver
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }
}
