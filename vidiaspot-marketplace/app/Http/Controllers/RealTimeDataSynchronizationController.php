<?php

namespace App\Http\Controllers;

use App\Services\RealTimeDataSynchronizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RealTimeDataSynchronizationController extends Controller
{
    private RealTimeDataSynchronizationService $syncService;

    public function __construct()
    {
        $this->syncService = new RealTimeDataSynchronizationService();
    }

    /**
     * Start real-time synchronization session for a user.
     */
    public function startRealTimeSync(Request $request)
    {
        $request->validate([
            'data_types' => 'array',
            'data_types.*' => 'string',
            'options' => 'array',
            'options.preferred_channel' => 'string|in:websocket,server_sent_events,polling,long_polling,http_streaming',
            'options.frequency' => 'string|in:auto,realtime,on_change,every_30_seconds,every_minute',
            'options.conflict_resolution' => 'string|in:last_write_wins,central_authority,merge_and_resolve,user_select,append_only',
            'options.device_type' => 'string|in:mobile,desktop,tablet',
            'options.bandwidth' => 'string|in:low,medium,high',
        ]);

        $userId = Auth::id();
        
        try {
            $result = $this->syncService->startRealTimeSync(
                $userId,
                $request->data_types ?? [],
                $request->options ?? []
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Synchronize data changes.
     */
    public function synchronizeData(Request $request, string $sessionId)
    {
        $request->validate([
            'data_type' => 'required|string',
            'changes' => 'required|array',
            'options' => 'array',
        ]);

        try {
            $result = $this->syncService->synchronizeData(
                $sessionId,
                $request->data_type,
                $request->changes,
                $request->options ?? []
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all syncable data types.
     */
    public function getSyncableDataTypes()
    {
        $dataTypes = $this->syncService->getSyncableDataTypes();

        return response()->json([
            'data_types' => $dataTypes,
            'message' => 'Syncable data types retrieved successfully'
        ]);
    }

    /**
     * Get available synchronizaton channels.
     */
    public function getSyncChannels()
    {
        $channels = $this->syncService->getSyncChannels();

        return response()->json([
            'channels' => $channels,
            'message' => 'Sync channels retrieved successfully'
        ]);
    }

    /**
     * Get conflict resolution strategies.
     */
    public function getConflictResolutionStrategies()
    {
        $strategies = $this->syncService->getConflictResolutionStrategies();

        return response()->json([
            'strategies' => $strategies,
            'message' => 'Conflict resolution strategies retrieved successfully'
        ]);
    }

    /**
     * Get user's active sync sessions.
     */
    public function getUserSyncSessions()
    {
        $userId = Auth::id();
        $sessions = $this->syncService->getUserSyncSessions($userId);

        return response()->json([
            'sessions' => $sessions,
            'message' => 'User sync sessions retrieved successfully'
        ]);
    }

    /**
     * Stop real-time synchronization session.
     */
    public function stopRealTimeSync(Request $request, string $sessionId)
    {
        $result = $this->syncService->stopRealTimeSync($sessionId);

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Real-time synchronization stopped successfully' : 'Session not found or already stopped'
        ]);
    }

    /**
     * Get sync history for a user.
     */
    public function getSyncHistory(Request $request)
    {
        $request->validate([
            'data_type' => 'string',
            'limit' => 'integer|min:1|max:100',
        ]);

        $userId = Auth::id();
        $history = $this->syncService->getSyncHistory(
            $userId,
            $request->data_type,
            $request->limit ?? 50
        );

        return response()->json([
            'history' => $history,
            'message' => 'Sync history retrieved successfully'
        ]);
    }

    /**
     * Get sync performance metrics.
     */
    public function getSyncPerformanceMetrics(Request $request)
    {
        $request->validate([
            'user_id' => 'string', // Admin access might need specific user metrics
        ]);

        $userId = $request->user_id ?? Auth::id();
        $metrics = $this->syncService->getSyncPerformanceMetrics($userId);

        return response()->json([
            'metrics' => $metrics,
            'message' => 'Sync performance metrics retrieved successfully'
        ]);
    }

    /**
     * Sync offline data when connection is restored.
     */
    public function syncOfflineData(Request $request)
    {
        $request->validate([
            'offline_changes' => 'required|array',
            'offline_changes.*.data_type' => 'required|string',
            'offline_changes.*.changes' => 'required|array',
            'offline_changes.*.timestamp' => 'required|date',
            'offline_changes.*.conflict_resolution' => 'string|in:last_write_wins,central_authority,merge_and_resolve,user_select,append_only',
        ]);

        $userId = Auth::id();
        $result = $this->syncService->syncOfflineData($userId, $request->offline_changes);

        return response()->json($result);
    }

    /**
     * Get recommended sync strategy based on user context.
     */
    public function getRecommendedSyncStrategy(Request $request)
    {
        $request->validate([
            'connection_quality' => 'string|in:poor,limited,good,excellent',
            'device_type' => 'string|in:mobile,desktop,tablet',
            'data_sensitivity' => 'string|in:normal,high,critical',
        ]);

        $userContext = $request->only(['connection_quality', 'device_type', 'data_sensitivity']);
        $strategy = $this->syncService->getRecommendedSyncStrategy($userContext);

        return response()->json([
            'strategy' => $strategy,
            'message' => 'Recommended sync strategy retrieved successfully'
        ]);
    }

    /**
     * Get sync status for a specific data type.
     */
    public function getSyncStatus(Request $request, string $dataType)
    {
        $request->validate([
            'user_id' => 'string', // Allow admin to check other users' status
        ]);

        $userId = $request->user_id ?? Auth::id();
        $status = $this->syncService->getSyncStatus($userId, $dataType);

        return response()->json([
            'status' => $status,
            'message' => 'Sync status retrieved successfully'
        ]);
    }

    /**
     * Force sync for critical data types.
     */
    public function forceSyncCriticalData(Request $request)
    {
        $request->validate([
            'data_types' => 'array',
            'data_types.*' => 'string',
        ]);

        $userId = Auth::id();
        $result = $this->syncService->forceSyncCriticalData($userId, $request->data_types);

        return response()->json([
            'result' => $result,
            'message' => 'Critical data sync forced successfully'
        ]);
    }
}