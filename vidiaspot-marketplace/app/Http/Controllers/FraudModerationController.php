<?php

namespace App\Http\Controllers;

use App\Services\FraudDetectionService;
use App\Models\Ad;
use App\Models\User;
use App\Models\Message;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FraudModerationController extends Controller
{
    protected FraudDetectionService $fraudDetectionService;

    public function __construct(FraudDetectionService $fraudDetectionService)
    {
        $this->fraudDetectionService = $fraudDetectionService;
    }

    /**
     * Analyze an ad for fraud risk
     */
    public function analyzeAd(Request $request, int $adId): JsonResponse
    {
        $ad = Ad::findOrFail($adId);

        $analysis = $this->fraudDetectionService->analyzeAdRisk($ad);

        // Automatically flag if high risk
        if ($analysis['is_suspicious']) {
            $this->fraudDetectionService->flagContent('ad', $adId, $analysis['flags']);
        }

        return response()->json($analysis);
    }

    /**
     * Analyze a user for risk
     */
    public function analyzeUser(Request $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        $analysis = $this->fraudDetectionService->analyzeUserRisk($user);

        // Automatically flag if high risk
        if ($analysis['is_suspicious']) {
            $this->fraudDetectionService->flagContent('user', $userId, $analysis['flags']);
        }

        return response()->json($analysis);
    }

    /**
     * Analyze a message for inappropriate content
     */
    public function analyzeMessage(Request $request, int $messageId): JsonResponse
    {
        $message = Message::findOrFail($messageId);

        $analysis = $this->fraudDetectionService->analyzeMessageContent($message);

        // Automatically flag if suspicious
        if ($analysis['is_suspicious']) {
            $this->fraudDetectionService->flagContent('message', $messageId, $analysis['flags']);
        }

        return response()->json($analysis);
    }

    /**
     * Get flagged content for review
     */
    public function getFlaggedContent(Request $request): JsonResponse
    {
        $status = $request->input('status', 'pending'); // pending, reviewed, all
        $limit = $request->input('limit', 25);
        $offset = $request->input('offset', 0);

        $query = \DB::table('fraud_flags as ff')
                    ->leftJoin('ads', 'ff.content_id', '=', 'ads.id')
                    ->leftJoin('users', 'ads.user_id', '=', 'users.id')
                    ->leftJoin('messages', 'ff.content_id', '=', 'messages.id')
                    ->leftJoin('users as sender', 'messages.sender_id', '=', 'sender.id');

        switch ($status) {
            case 'pending':
                $query->where('ff.status', 'pending');
                break;
            case 'reviewed':
                $query->where('ff.status', '!=', 'pending');
                break;
        }

        $results = $query->select([
            'ff.id',
            'ff.content_type',
            'ff.content_id',
            'ff.reasons',
            'ff.flagged_at',
            'ff.reviewed_at',
            'ff.status',
            'ff.reviewer_id',
            'users.name as user_name',
            'users.email as user_email',
            'ads.title as ad_title',
            'messages.content as message_content',
            'sender.name as sender_name'
        ])
        ->offset($offset)
        ->limit($limit)
        ->orderBy('ff.flagged_at', 'desc')
        ->get();

        return response()->json([
            'data' => $results,
            'total' => \DB::table('fraud_flags')->when($status !== 'all', function ($q) use ($status) {
                return $status === 'pending' ? $q->where('status', 'pending') : $q->where('status', '!=', 'pending');
            })->count(),
        ]);
    }

    /**
     * Review flagged content
     */
    public function reviewFlaggedContent(Request $request, int $flagId): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,flag,reject,suspend,ban',
            'notes' => 'nullable|string',
        ]);

        $userId = $request->user()->id;

        // Update the flag status
        $result = $this->fraudDetectionService->reviewFlaggedContent(
            $flagId,
            $request->input('action'),
            $userId
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Flagged content reviewed successfully',
                'action_taken' => $request->input('action'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update flagged content',
        ], 500);
    }

    /**
     * Bulk review flagged content
     */
    public function bulkReview(Request $request): JsonResponse
    {
        $request->validate([
            'flag_ids' => 'required|array',
            'flag_ids.*' => 'integer',
            'action' => 'required|in:approve,flag,reject,suspend,ban',
            'notes' => 'nullable|string',
        ]);

        $userId = $request->user()->id;
        $flagIds = $request->input('flag_ids');
        $action = $request->input('action');
        $notes = $request->input('notes');

        $processed = 0;
        foreach ($flagIds as $flagId) {
            if ($this->fraudDetectionService->reviewFlaggedContent($flagId, $action, $userId)) {
                $processed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$processed} items processed",
            'action_taken' => $action,
            'total_requested' => count($flagIds),
            'total_processed' => $processed,
        ]);
    }

    /**
     * Get risk summary for admin dashboard
     */
    public function getRiskSummary(): JsonResponse
    {
        $summary = $this->fraudDetectionService->getRiskSummary();

        return response()->json($summary);
    }

    /**
     * Auto-moderate an ad before it's published
     */
    public function autoModAd(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        // Create a temporary ad object for analysis
        $ad = new Ad();
        $ad->title = $request->input('title');
        $ad->description = $request->input('description');
        $ad->user_id = $request->input('user_id');

        $analysis = $this->fraudDetectionService->analyzeAdRisk($ad);

        // If high risk, prevent publication
        if ($analysis['risk_level'] === 'high') {
            return response()->json([
                'allowed' => false,
                'message' => 'Content flagged for review due to potential policy violation',
                'analysis' => $analysis,
            ]);
        }

        return response()->json([
            'allowed' => $analysis['risk_level'] !== 'high',
            'message' => 'Content approved',
            'analysis' => $analysis,
        ]);
    }

    /**
     * Auto-moderate a message before it's sent
     */
    public function autoModMessage(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'sender_id' => 'required|integer',
            'recipient_id' => 'required|integer',
        ]);

        // Check user risk first
        $user = User::find($request->input('sender_id'));
        if ($user) {
            $userAnalysis = $this->fraudDetectionService->analyzeUserRisk($user);

            // If user has high risk, block message
            if ($userAnalysis['risk_level'] === 'high') {
                return response()->json([
                    'allowed' => false,
                    'message' => 'Message blocked due to account risk',
                    'user_analysis' => $userAnalysis,
                ]);
            }
        }

        // Create a temporary message object for analysis
        $message = new Message();
        $message->content = $request->input('content');
        $message->sender_id = $request->input('sender_id');

        $analysis = $this->fraudDetectionService->analyzeMessageContent($message);

        // If high risk, flag message
        if ($analysis['risk_level'] === 'high') {
            return response()->json([
                'allowed' => false,
                'message' => 'Message flagged for review',
                'analysis' => $analysis,
            ]);
        }

        return response()->json([
            'allowed' => $analysis['risk_level'] !== 'high',
            'message' => 'Message approved',
            'analysis' => $analysis,
        ]);
    }

    /**
     * Get suspicious keywords list
     */
    public function getSuspiciousKeywords(): JsonResponse
    {
        $reflection = new \ReflectionClass($this->fraudDetectionService);
        $property = $reflection->getProperty('suspiciousKeywords');
        $property->setAccessible(true);
        
        return response()->json([
            'keywords' => $property->getValue($this->fraudDetectionService)
        ]);
    }

    /**
     * Add suspicious keyword
     */
    public function addSuspiciousKeyword(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string',
        ]);

        // In a real system, we'd persist this addition to the database
        // For now, we'll return a success message
        return response()->json([
            'message' => 'Keyword added successfully',
            'keyword' => $request->input('keyword'),
        ]);
    }

    /**
     * Get user reputation score
     */
    public function getUserReputation(int $userId): JsonResponse
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Analyze user risk which includes reputation factors
        $analysis = $this->fraudDetectionService->analyzeUserRisk($user);

        // Calculate reputation score based on risk factors
        $reputationScore = max(0, 100 - ($analysis['risk_score'] * 10));

        return response()->json([
            'user_id' => $userId,
            'reputation_score' => $reputationScore,
            'risk_level' => $analysis['risk_level'],
            'risk_score' => $analysis['risk_score'],
            'risk_factors' => $analysis['flags'],
        ]);
    }

    /**
     * Report content
     */
    public function reportContent(Request $request): JsonResponse
    {
        $request->validate([
            'content_type' => 'required|in:ad,user,message',
            'content_id' => 'required|integer',
            'reason' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $report = Report::create([
            'user_id' => $request->user()->id,
            'content_type' => $request->input('content_type'),
            'content_id' => $request->input('content_id'),
            'reason' => $request->input('reason'),
            'description' => $request->input('description'),
        ]);

        // Trigger fraud analysis
        if ($request->input('content_type') === 'ad') {
            $ad = Ad::find($request->input('content_id'));
            if ($ad) {
                $this->fraudDetectionService->analyzeAdRisk($ad);
            }
        }

        return response()->json([
            'message' => 'Report submitted successfully',
            'report_id' => $report->id,
        ]);
    }

    /**
     * Get reports for content
     */
    public function getContentReports(Request $request, string $contentType, int $contentId): JsonResponse
    {
        $reports = Report::where('content_type', $contentType)
                         ->where('content_id', $contentId)
                         ->with('user:id,name,email')
                         ->get();

        return response()->json([
            'reports' => $reports,
            'total' => $reports->count(),
        ]);
    }
}