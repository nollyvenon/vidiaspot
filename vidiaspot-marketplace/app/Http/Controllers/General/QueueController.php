<?php

namespace App\Http\Controllers\General;

use App\Services\QueueService;
use App\Jobs\SendEmailJob;
use App\Jobs\SendNotificationJob;
use App\Jobs\SearchIndexingJob;
use App\Jobs\ProcessPaymentJob;
use App\Jobs\ProcessImageJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QueueController extends Controller
{
    protected QueueService $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * Get queue statistics
     */
    public function stats(): JsonResponse
    {
        $stats = $this->queueService->getAllQueueStats();
        $performance = $this->queueService->monitorQueuePerformance();

        return response()->json([
            'queue_stats' => $stats,
            'performance' => $performance,
        ]);
    }

    /**
     * Process an email job
     */
    public function processEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'template' => 'required|string',
            'variables' => 'array',
        ]);

        $this->queueService->addToEmailQueue(SendEmailJob::class, [
            'to' => $validated['to'],
            'subject' => $validated['subject'],
            'template' => $validated['template'],
            'variables' => $validated['variables'] ?? [],
        ]);

        return response()->json([
            'message' => 'Email job added to queue successfully',
        ]);
    }

    /**
     * Process a notification job
     */
    public function processNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'message' => 'required|string',
            'type' => 'string|in:info,success,error,warning',
            'data' => 'array',
        ]);

        $this->queueService->addToNotificationQueue(SendNotificationJob::class, [
            'user_id' => $validated['user_id'],
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'info',
            'data' => $validated['data'] ?? [],
        ]);

        return response()->json([
            'message' => 'Notification job added to queue successfully',
        ]);
    }

    /**
     * Process a search indexing job
     */
    public function processSearchIndexing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|array',
            'operation' => 'required|in:index,update,delete',
        ]);

        $this->queueService->addToSearchQueue(SearchIndexingJob::class, [
            'data' => $validated['data'],
            'operation' => $validated['operation'],
        ]);

        return response()->json([
            'message' => 'Search indexing job added to queue successfully',
        ]);
    }

    /**
     * Process a payment job
     */
    public function processPayment(Request $request, int $paymentId): JsonResponse
    {
        $this->queueService->addToPaymentQueue(ProcessPaymentJob::class, [
            'payment_id' => $paymentId,
        ]);

        return response()->json([
            'message' => 'Payment processing job added to queue successfully',
            'payment_id' => $paymentId,
        ]);
    }

    /**
     * Process an image job
     */
    public function processImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image_path' => 'required|string',
            'operations' => 'array',
        ]);

        $this->queueService->addToImageQueue(ProcessImageJob::class, [
            'image_path' => $validated['image_path'],
            'operations' => $validated['operations'] ?? [],
        ]);

        return response()->json([
            'message' => 'Image processing job added to queue successfully',
            'image_path' => $validated['image_path'],
        ]);
    }

    /**
     * Clean up failed jobs
     */
    public function cleanupFailedJobs(Request $request): JsonResponse
    {
        $queue = $request->input('queue');

        $deletedCount = $this->queueService->cleanupFailedJobs($queue);

        return response()->json([
            'message' => 'Failed jobs cleanup completed',
            'deleted_count' => $deletedCount,
            'queue' => $queue ?? 'all',
        ]);
    }

    /**
     * Retry failed jobs
     */
    public function retryFailedJobs(Request $request): JsonResponse
    {
        $jobId = $request->input('job_id');

        $success = $this->queueService->retryFailedJobs($jobId);

        return response()->json([
            'message' => $jobId ? 'Specific failed job retried' : 'All failed jobs retried',
            'success' => $success,
            'job_id' => $jobId,
        ]);
    }

    /**
     * Get queue health check
     */
    public function healthCheck(): JsonResponse
    {
        $stats = $this->queueService->getAllQueueStats();
        $overallHealth = 'healthy';
        
        foreach ($stats as $stat) {
            if ($stat['size'] > 100) { // Threshold could be configurable
                $overallHealth = 'warning';
                break;
            }
        }

        return response()->json([
            'status' => 'success',
            'health' => $overallHealth,
            'timestamp' => now(),
            'queues' => $stats,
        ]);
    }

    /**
     * Bulk queue operations
     */
    public function bulkQueueOperations(Request $request): JsonResponse
    {
        $operations = $request->validate([
            'jobs' => 'required|array',
            'jobs.*.type' => 'required|in:email,notification,search,payment,image',
            'jobs.*.data' => 'required|array',
        ]);

        $results = [];
        foreach ($operations['jobs'] as $job) {
            $result = null;
            
            switch ($job['type']) {
                case 'email':
                    $this->queueService->addToEmailQueue(SendEmailJob::class, $job['data']);
                    $result = ['type' => 'email', 'status' => 'added'];
                    break;
                case 'notification':
                    $this->queueService->addToNotificationQueue(SendNotificationJob::class, $job['data']);
                    $result = ['type' => 'notification', 'status' => 'added'];
                    break;
                case 'search':
                    $this->queueService->addToSearchQueue(SearchIndexingJob::class, $job['data']);
                    $result = ['type' => 'search', 'status' => 'added'];
                    break;
                case 'payment':
                    $this->queueService->addToPaymentQueue(ProcessPaymentJob::class, $job['data']);
                    $result = ['type' => 'payment', 'status' => 'added'];
                    break;
                case 'image':
                    $this->queueService->addToImageQueue(ProcessImageJob::class, $job['data']);
                    $result = ['type' => 'image', 'status' => 'added'];
                    break;
                default:
                    $result = ['type' => $job['type'], 'status' => 'invalid'];
                    break;
            }
            
            $results[] = $result;
        }

        return response()->json([
            'message' => 'Bulk queue operations completed',
            'results' => $results,
            'total_processed' => count($results),
        ]);
    }
}