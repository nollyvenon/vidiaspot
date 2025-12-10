<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\VideoCall;
use App\Models\Scheduling;
use App\Models\Escrow;
use App\Models\Ad;
use App\Services\SmartMessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartMessagingController extends Controller
{
    protected $smartMessagingService;

    public function __construct(SmartMessagingService $smartMessagingService)
    {
        $this->smartMessagingService = $smartMessagingService;
    }

    /**
     * Get smart reply suggestions
     */
    public function getSmartReplies(Request $request)
    {
        $message = $request->input('message', '');
        $context = $request->input('context', []);

        $replies = $this->smartMessagingService->getSmartReplies($message, $context);

        return response()->json([
            'success' => true,
            'replies' => $replies
        ]);
    }

    /**
     * Translate message content
     */
    public function translateMessage(Request $request)
    {
        $text = $request->input('text');
        $from = $request->input('from', 'en');
        $to = $request->input('to', 'en');

        if (!$text) {
            return response()->json([
                'error' => 'Text is required'
            ], 400);
        }

        $translated = $this->smartMessagingService->translateMessage($text, $from, $to);

        return response()->json([
            'success' => true,
            'original' => $text,
            'translated' => $translated,
            'from' => $from,
            'to' => $to
        ]);
    }

    /**
     * Start a conversation
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ad_id' => 'nullable|exists:ads,id',
        ]);

        $user = Auth::user();
        $otherUserId = $request->user_id;
        $adId = $request->ad_id;

        $conversation = $this->smartMessagingService->getOrCreateConversation(
            $user->id,
            $otherUserId,
            $adId
        );

        return response()->json([
            'success' => true,
            'conversation' => $conversation
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string',
            'message_type' => 'sometimes|string|in:text,image,voice,video,file',
        ]);

        $user = Auth::user();
        $messageType = $request->input('message_type', 'text');
        $content = $request->content;
        $conversationId = $request->conversation_id;

        // Check if user has access to this conversation
        $conversation = Conversation::find($conversationId);
        if (!$conversation || ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id)) {
            return response()->json([
                'error' => 'Unauthorized to send message in this conversation'
            ], 403);
        }

        $additionalData = [
            'language' => $request->input('language', 'en'),
            'metadata' => $request->input('metadata', [])
        ];

        $message = $this->smartMessagingService->sendMessage(
            $conversationId,
            $user->id,
            $content,
            $messageType,
            $additionalData
        );

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'receiver')
        ]);
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory($conversationId)
    {
        $user = Auth::user();

        // Check if user has access to this conversation
        $conversation = Conversation::find($conversationId);
        if (!$conversation || ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id)) {
            return response()->json([
                'error' => 'Unauthorized to access this conversation'
            ], 403);
        }

        $messages = $this->smartMessagingService->getConversationHistory($conversationId);
        $this->smartMessagingService->markMessagesAsRead($user->id, $messages->pluck('id')->toArray());

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'conversation' => $conversation
        ]);
    }

    /**
     * Get user's conversations
     */
    public function getUserConversations()
    {
        $user = Auth::user();

        $conversations = Conversation::where(function($query) use ($user) {
            $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })
        ->with(['user1', 'user2', 'ad'])
        ->orderBy('last_message_at', 'desc')
        ->get();

        // For each conversation, get the latest message
        foreach ($conversations as $conversation) {
            $latestMessage = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->first();
            $conversation->latest_message = $latestMessage;
            $conversation->unread_count = $conversation->messages()
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        return response()->json([
            'success' => true,
            'conversations' => $conversations
        ]);
    }

    /**
     * Schedule a meeting/pickup
     */
    public function scheduleMeeting(Request $request)
    {
        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'recipient_user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'scheduled_datetime' => 'required|date',
            'location' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $ad = Ad::find($request->ad_id);

        // Check if user is buyer or seller for this ad
        if ($ad->user_id !== $user->id && $request->recipient_user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized to schedule meeting for this ad'
            ], 403);
        }

        $schedule = $this->smartMessagingService->scheduleMeeting(
            $user->id,
            $request->recipient_user_id,
            $request->ad_id,
            $request->only(['title', 'description', 'scheduled_datetime', 'location'])
        );

        return response()->json([
            'success' => true,
            'schedule' => $schedule
        ]);
    }

    /**
     * Create a video call
     */
    public function createVideoCall(Request $request)
    {
        $request->validate([
            'recipient_user_id' => 'required|exists:users,id',
            'ad_id' => 'nullable|exists:ads,id',
            'call_type' => 'sometimes|string|in:video,audio',
            'scheduled_at' => 'nullable|date',
        ]);

        $user = Auth::user();

        $call = $this->smartMessagingService->createVideoCall(
            $user->id,
            $request->recipient_user_id,
            $request->ad_id,
            $request->only(['call_type', 'scheduled_at'])
        );

        return response()->json([
            'success' => true,
            'call' => $call
        ]);
    }

    /**
     * Create an escrow for a transaction
     */
    public function createEscrow(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
            'ad_id' => 'required|exists:ads,id',
            'seller_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $ad = Ad::find($request->ad_id);

        // Verify that the current user is the buyer (the one making the purchase)
        if ($ad->user_id === $user->id) {
            return response()->json([
                'error' => 'Seller cannot create escrow for own ad'
            ], 400);
        }

        $escrow = $this->smartMessagingService->createEscrow(
            $request->transaction_id,
            $request->ad_id,
            $user->id,
            $request->seller_user_id,
            $request->amount,
            $request->currency ?? 'NGN'
        );

        return response()->json([
            'success' => true,
            'escrow' => $escrow
        ]);
    }

    /**
     * Release escrow funds to seller
     */
    public function releaseEscrow(Request $request, $escrowId)
    {
        $user = Auth::user();

        // Get the escrow and check if the user has permission to release
        $escrow = Escrow::find($escrowId);
        if (!$escrow) {
            return response()->json([
                'error' => 'Escrow not found'
            ], 404);
        }

        // Only seller or admin can release the escrow
        if ($user->id !== $escrow->seller_user_id && !$user->hasRole('admin')) {
            return response()->json([
                'error' => 'Unauthorized to release this escrow'
            ], 403);
        }

        $result = $this->smartMessagingService->releaseEscrow($escrowId);

        return response()->json($result);
    }

    /**
     * Verify escrow on blockchain
     */
    public function verifyEscrowOnBlockchain($escrowId)
    {
        $user = Auth::user();

        // Get the escrow and check if the user has permission to verify
        $escrow = Escrow::find($escrowId);
        if (!$escrow) {
            return response()->json([
                'error' => 'Escrow not found'
            ], 404);
        }

        // Both parties or admin can verify the escrow
        if ($user->id !== $escrow->buyer_user_id && $user->id !== $escrow->seller_user_id && !$user->hasRole('admin')) {
            return response()->json([
                'error' => 'Unauthorized to verify this escrow'
            ], 403);
        }

        $result = $this->smartMessagingService->verifyEscrowOnBlockchain($escrowId);

        return response()->json($result);
    }

    /**
     * Resolve an escrow dispute
     */
    public function resolveEscrowDispute(Request $request, $escrowId)
    {
        $request->validate([
            'dispute_details' => 'required|array',
            'dispute_details.reason' => 'required|string',
            'dispute_details.evidence' => 'required|array',
        ]);

        $user = Auth::user();

        // Get the escrow and check if the user has permission to resolve dispute
        $escrow = Escrow::find($escrowId);
        if (!$escrow) {
            return response()->json([
                'error' => 'Escrow not found'
            ], 404);
        }

        // Only admins or parties involved can resolve disputes
        if ($user->id !== $escrow->buyer_user_id && $user->id !== $escrow->seller_user_id && !$user->hasRole('admin')) {
            return response()->json([
                'error' => 'Unauthorized to resolve this dispute'
            ], 403);
        }

        $result = $this->smartMessagingService->resolveEscrowDispute($escrowId, $request->dispute_details);

        return response()->json($result);
    }

    /**
     * Get user's notifications
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $unreadCount = $this->smartMessagingService->getUnreadMessageCount($user->id);

        return response()->json([
            'success' => true,
            'notifications' => [
                'unread_messages' => $unreadCount,
            ]
        ]);
    }

    /**
     * Mark notifications as read
     */
    public function markNotificationsAsRead(Request $request)
    {
        $user = Auth::user();
        $messageIds = $request->input('message_ids');

        $this->smartMessagingService->markMessagesAsRead($user->id, $messageIds);

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read'
        ]);
    }
}