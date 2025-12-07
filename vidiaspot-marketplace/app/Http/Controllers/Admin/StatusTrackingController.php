<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ad;
use App\Models\Vendor;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\StatusLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StatusTrackingController extends Controller
{
    /**
     * Display detailed status tracking for all entities.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = StatusLog::with(['user', 'statusable'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('entity_type')) {
            $query->where('statusable_type', $request->entity_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $statusLogs = $query->paginate(25);

        $entityTypes = ['Ad', 'Vendor', 'Payment', 'Subscription'];

        return $this->adminView('admin.status-tracking.index', [
            'statusLogs' => $statusLogs,
            'entityTypes' => $entityTypes,
        ]);
    }

    /**
     * Get status history for a specific entity.
     */
    public function getEntityHistory(Request $request, string $entityType, int $entityId): JsonResponse
    {
        $this->checkAdminAccess();

        $model = null;
        switch ($entityType) {
            case 'ad':
                $model = Ad::find($entityId);
                break;
            case 'vendor':
                $model = Vendor::find($entityId);
                break;
            case 'payment':
                $model = Payment::find($entityId);
                break;
            case 'subscription':
                $model = Subscription::find($entityId);
                break;
            default:
                return response()->json(['error' => 'Invalid entity type'], 400);
        }

        if (!$model) {
            return response()->json(['error' => 'Entity not found'], 404);
        }

        $statusLogs = StatusLog::where([
            'statusable_type' => get_class($model),
            'statusable_id' => $entityId,
        ])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'entity' => $model,
            'status_history' => $statusLogs,
        ]);
    }

    /**
     * Get status statistics.
     */
    public function getStats(): JsonResponse
    {
        $this->checkAdminAccess();

        $totalStatusLogs = StatusLog::count();
        $recentStatusLogs = StatusLog::with(['user', 'statusable'])->latest()->take(10)->get();
        $statusCounts = StatusLog::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'total_logs' => $totalStatusLogs,
            'recent_logs' => $recentStatusLogs,
            'status_counts' => $statusCounts,
        ]);
    }
}