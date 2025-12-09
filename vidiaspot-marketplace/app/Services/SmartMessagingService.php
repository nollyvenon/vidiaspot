<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\VideoCall;
use App\Models\Scheduling;
use App\Models\Escrow;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SmartMessagingService
{
    /**
     * Get AI-powered smart reply suggestions
     */
    public function getSmartReplies($messageContent, $context = [])
    {
        // In a real implementation, this would call an AI service
        // For now, we'll return some common smart replies based on keywords
        
        $replies = [
            'positive' => [
                'That sounds good!',
                'I\'m interested, can you tell me more?',
                'When are you available to meet?',
                'What time works for you?',
                'Thanks for the information!'
            ],
            'negative' => [
                'I\'m not interested, thanks',
                'That\'s too expensive for me',
                'I found something else',
                'I\'ll think about it'
            ],
            'neutral' => [
                'Can you provide more details?',
                'Where is it located?',
                'Is it in good condition?',
                'Can I come to see it?',
                'How long have you had this?'
            ]
        ];
        
        // Determine which set of replies to use based on the message content
        $lowerContent = strtolower($messageContent);
        
        if (strpos($lowerContent, 'price') !== false || strpos($lowerContent, 'cost') !== false) {
            return [
                'Can you negotiate the price?',
                'Is the price firm?',
                'Would you consider any offers?',
                'What is your best price?'
            ];
        }
        
        if (strpos($lowerContent, 'when') !== false || strpos($lowerContent, 'time') !== false) {
            return [
                'I\'m available on weekends',
                'Weekdays work better for me',
                'Morning or afternoon?',
                'What time is convenient for you?'
            ];
        }
        
        // Return generic appropriate replies based on sentiment
        return $replies['neutral'];
    }
    
    /**
     * Translate message content between languages
     */
    public function translateMessage($text, $fromLanguage = 'en', $toLanguage = 'en')
    {
        if ($fromLanguage === $toLanguage) {
            return $text;
        }
        
        // In a real implementation, this would call a translation API
        // For now, return the original text with a note
        return $text; // Placeholder - would call Google Translate API or similar
    }
    
    /**
     * Transcribe voice message content
     */
    public function transcribeVoiceMessage($audioFilePath)
    {
        // In a real implementation, this would call a speech-to-text API
        // For now, return a placeholder
        return "Transcription of voice message: [Transcription would appear here if this were a real implementation]";
    }
    
    /**
     * Create a new conversation or get existing one
     */
    public function getOrCreateConversation($user1Id, $user2Id, $adId = null)
    {
        // Check if a conversation already exists between these two users for this ad
        $conversation = Conversation::where(function($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user1Id)->where('user2_id', $user2Id);
        })->orWhere(function($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user2Id)->where('user2_id', $user1Id);
        })->when($adId, function($query) use ($adId) {
            $query->where('ad_id', $adId);
        })->first();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
                'ad_id' => $adId,
                'is_active' => true,
            ]);
        }
        
        return $conversation;
    }
    
    /**
     * Send a message in a conversation
     */
    public function sendMessage($conversationId, $senderId, $content, $messageType = 'text', $additionalData = [])
    {
        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'receiver_id' => $this->getReceiverId($conversationId, $senderId),
            'content' => $content,
            'message_type' => $messageType,
            'language' => $additionalData['language'] ?? 'en',
            'translated_content' => $additionalData['translated_content'] ?? null,
            'metadata' => $additionalData['metadata'] ?? [],
            'status' => 'sent',
        ]);
        
        // Update the last message time in conversation
        $conversation = Conversation::find($conversationId);
        $conversation->update(['last_message_at' => now()]);
        
        return $message;
    }
    
    /**
     * Get the receiver ID in a conversation
     */
    private function getReceiverId($conversationId, $senderId)
    {
        $conversation = Conversation::find($conversationId);
        if ($conversation->user1_id == $senderId) {
            return $conversation->user2_id;
        }
        return $conversation->user1_id;
    }
    
    /**
     * Get conversation history with message metadata
     */
    public function getConversationHistory($conversationId, $limit = 50, $offset = 0)
    {
        return Message::where('conversation_id', $conversationId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->reverse(); // Reverse to get chronological order
    }
    
    /**
     * Schedule a meeting/pickup
     */
    public function scheduleMeeting($initiatorUserId, $recipientUserId, $adId, $data)
    {
        return Scheduling::create([
            'ad_id' => $adId,
            'initiator_user_id' => $initiatorUserId,
            'recipient_user_id' => $recipientUserId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'scheduled_datetime' => $data['scheduled_datetime'],
            'location' => $data['location'],
            'participants' => $data['participants'] ?? [],
            'preferences' => $data['preferences'] ?? [],
            'type' => $data['type'] ?? 'pickup',
            'notes' => $data['notes'] ?? null,
        ]);
    }
    
    /**
     * Create a video call
     */
    public function createVideoCall($initiatorUserId, $recipientUserId, $adId = null, $data = [])
    {
        return VideoCall::create([
            'ad_id' => $adId,
            'initiator_user_id' => $initiatorUserId,
            'recipient_user_id' => $recipientUserId,
            'room_id' => Str::uuid(),
            'call_type' => $data['call_type'] ?? 'video',
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'participants' => $data['participants'] ?? [],
            'status' => $data['scheduled_at'] ? 'pending' : 'ongoing', // If scheduled, it's pending; otherwise ongoing
            'settings' => $data['settings'] ?? [],
        ]);
    }
    
    /**
     * Create an escrow for a transaction
     */
    public function createEscrow($transactionId, $adId, $buyerId, $sellerId, $amount, $currency = 'NGN')
    {
        // In a real implementation, we would create a blockchain transaction here
        $blockchainService = new \App\Services\BlockchainService();

        // Create a simulated blockchain transaction for the escrow
        $blockchainTransaction = $blockchainService->createTransaction(
            // In a real app, these would be actual blockchain addresses
            '0x' . bin2hex(random_bytes(20)), // buyer blockchain address
            '0x' . bin2hex(random_bytes(20)), // escrow contract address
            $amount,
            $currency,
            [
                'transaction_id' => $transactionId,
                'ad_id' => $adId,
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId
            ]
        );

        return Escrow::create([
            'transaction_id' => $transactionId,
            'ad_id' => $adId,
            'buyer_user_id' => $buyerId,
            'seller_user_id' => $sellerId,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'blockchain_transaction_hash' => $blockchainTransaction['transaction_hash'],
            'blockchain_contract_address' => $blockchainTransaction['to_address'],
            'blockchain_status' => $blockchainTransaction['status'],
            'blockchain_verification_data' => $blockchainTransaction,
        ]);
    }

    /**
     * Release escrow funds to seller with blockchain verification
     */
    public function releaseEscrow($escrowId)
    {
        $escrow = Escrow::find($escrowId);
        if (!$escrow) {
            return ['success' => false, 'message' => 'Escrow not found'];
        }

        if ($escrow->status !== 'pending') {
            return ['success' => false, 'message' => 'Escrow is not in pending state'];
        }

        // Use blockchain service to execute the release
        $blockchainService = new \App\Services\BlockchainService();

        $releaseResult = $blockchainService->executeEscrowRelease(
            $escrow->blockchain_contract_address,
            // In a real app, this would be the seller's blockchain address
            '0x' . bin2hex(random_bytes(20))
        );

        if ($releaseResult['success']) {
            $escrow->update([
                'status' => 'released',
                'blockchain_status' => 'executed',
                'blockchain_verification_data' => array_merge(
                    $escrow->blockchain_verification_data ?? [],
                    ['release_transaction' => $releaseResult]
                ),
            ]);

            return [
                'success' => true,
                'message' => 'Escrow released successfully',
                'transaction_hash' => $releaseResult['transaction_hash']
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to release escrow on blockchain'
        ];
    }

    /**
     * Verify escrow transaction on blockchain
     */
    public function verifyEscrowOnBlockchain($escrowId)
    {
        $escrow = Escrow::find($escrowId);
        if (!$escrow || !$escrow->blockchain_transaction_hash) {
            return ['success' => false, 'message' => 'Escrow or blockchain transaction not found'];
        }

        $blockchainService = new \App\Services\BlockchainService();

        $verification = $blockchainService->verifyTransactionByHash($escrow->blockchain_transaction_hash);

        // Update escrow with verification data
        $escrow->update([
            'blockchain_status' => $verification['status'],
            'blockchain_verification_data' => array_merge(
                $escrow->blockchain_verification_data ?? [],
                $verification
            ),
        ]);

        return [
            'success' => true,
            'verification' => $verification
        ];
    }
    
    /**
     * Resolve an escrow dispute using AI (simulated)
     */
    public function resolveEscrowDispute($escrowId, $disputeDetails)
    {
        $escrow = Escrow::find($escrowId);
        
        if (!$escrow) {
            return ['success' => false, 'message' => 'Escrow not found'];
        }
        
        // Simulate AI dispute resolution by analyzing details
        $resolution = $this->analyzeDispute($disputeDetails);
        
        $escrow->update([
            'dispute_status' => 'resolved',
            'dispute_resolved_at' => now(),
            'dispute_details' => array_merge($escrow->dispute_details ?? [], $disputeDetails),
            'status' => $resolution['resolution'],
        ]);
        
        return [
            'success' => true,
            'resolution' => $resolution,
            'message' => 'Dispute resolved successfully'
        ];
    }
    
    /**
     * Analyze a dispute (simulated AI logic)
     */
    private function analyzeDispute($disputeDetails)
    {
        // This is a simplified version - in reality, this would use ML algorithms
        $evidence = $disputeDetails['evidence'] ?? [];
        
        // Simple logic to determine resolution
        if (count($evidence) > 0) {
            // If there's evidence, favor the party with more evidence
            $buyerEvidence = array_filter($evidence, function($item) {
                return $item['party'] === 'buyer';
            });
            
            $sellerEvidence = array_filter($evidence, function($item) {
                return $item['party'] === 'seller';
            });
            
            if (count($buyerEvidence) > count($sellerEvidence)) {
                return [
                    'resolution' => 'refunded',
                    'reason' => 'Buyer provided more supporting evidence',
                    'decision' => 'Funds refunded to buyer'
                ];
            } else {
                return [
                    'resolution' => 'released',
                    'reason' => 'Seller provided more supporting evidence',
                    'decision' => 'Funds released to seller'
                ];
            }
        }
        
        // Default resolution if no clear evidence
        return [
            'resolution' => 'released',
            'reason' => 'Insufficient evidence provided - following standard procedure',
            'decision' => 'Funds released to seller after verification period'
        ];
    }
    
    /**
     * Get user's unread messages count
     */
    public function getUnreadMessageCount($userId)
    {
        return Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }
    
    /**
     * Mark messages as read
     */
    public function markMessagesAsRead($userId, $messageIds = null)
    {
        $query = Message::where('receiver_id', $userId);
        
        if ($messageIds) {
            $query->whereIn('id', $messageIds);
        } else {
            $query->where('is_read', false);
        }
        
        return $query->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}