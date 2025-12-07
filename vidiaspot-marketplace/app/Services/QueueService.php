<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class QueueService
{
    /**
     * Add a job to the queue
     *
     * @param string $jobClass The job class name
     * @param array $data Job data
     * @param string $queue Queue name (optional)
     * @param int $delay Delay in seconds (optional)
     * @return mixed
     */
    public function addJob(string $jobClass, array $data, string $queue = null, int $delay = 0)
    {
        $job = new $jobClass($data);
        
        if ($delay > 0) {
            return Queue::later($delay, $job, $queue);
        } else {
            return Queue::push($job, null, $queue);
        }
    }

    /**
     * Add delayed job to the queue
     *
     * @param string $jobClass The job class name
     * @param array $data Job data
     * @param int $delay Delay in seconds
     * @param string $queue Queue name (optional)
     * @return mixed
     */
    public function addDelayedJob(string $jobClass, array $data, int $delay, string $queue = null)
    {
        return $this->addJob($jobClass, $data, $queue, $delay);
    }

    /**
     * Add a job to the email queue
     *
     * @param string $emailJobClass The email job class
     * @param array $emailData Email data
     * @return mixed
     */
    public function addToEmailQueue(string $emailJobClass, array $emailData)
    {
        return $this->addJob($emailJobClass, $emailData, 'emails');
    }

    /**
     * Add a job to the notification queue
     *
     * @param string $notificationJobClass The notification job class
     * @param array $notificationData Notification data
     * @return mixed
     */
    public function addToNotificationQueue(string $notificationJobClass, array $notificationData)
    {
        return $this->addJob($notificationJobClass, $notificationData, 'notifications');
    }

    /**
     * Add a job to the image processing queue
     *
     * @param string $imageJobClass The image job class
     * @param array $imageData Image data
     * @return mixed
     */
    public function addToImageQueue(string $imageJobClass, array $imageData)
    {
        return $this->addJob($imageJobClass, $imageData, 'images');
    }

    /**
     * Add a job to the search index queue
     *
     * @param string $searchJobClass The search job class
     * @param array $searchData Search data
     * @return mixed
     */
    public function addToSearchQueue(string $searchJobClass, array $searchData)
    {
        return $this->addJob($searchJobClass, $searchData, 'search');
    }

    /**
     * Add a job to the payment processing queue
     *
     * @param string $paymentJobClass The payment job class
     * @param array $paymentData Payment data
     * @return mixed
     */
    public function addToPaymentQueue(string $paymentJobClass, array $paymentData)
    {
        return $this->addJob($paymentJobClass, $paymentData, 'payments');
    }

    /**
     * Get queue stats
     *
     * @param string $queue Queue name
     * @return array
     */
    public function getQueueStats(string $queue = 'default'): array
    {
        $connection = Queue::connection();
        $size = 0;
        
        if (method_exists($connection, 'size')) {
            $size = $connection->size($queue);
        }
        
        return [
            'queue' => $queue,
            'size' => $size,
            'connection' => $connection->getName(),
        ];
    }

    /**
     * Get all queue stats
     *
     * @return array
     */
    public function getAllQueueStats(): array
    {
        $queues = ['default', 'emails', 'notifications', 'images', 'search', 'payments'];
        $stats = [];
        
        foreach ($queues as $queue) {
            $stats[] = $this->getQueueStats($queue);
        }
        
        return $stats;
    }

    /**
     * Clean up failed jobs
     *
     * @param string|null $queue Queue name or null for all queues
     * @return int Number of deleted jobs
     */
    public function cleanupFailedJobs(string $queue = null): int
    {
        $connection = Queue::connection();
        
        if (method_exists($connection, 'getFailedJobs')) {
            $failedJobs = $connection->getFailedJobs($queue ?? 'default');
            
            $count = 0;
            foreach ($failedJobs as $job) {
                $connection->pruneFailedJobs();
                $count++;
            }
            
            return $count;
        }
        
        return 0;
    }

    /**
     * Retry failed jobs
     *
     * @param string|null $jobId Job ID or null for all failed jobs
     * @return bool
     */
    public function retryFailedJobs(string $jobId = null): bool
    {
        $connection = Queue::connection();
        
        if (method_exists($connection, 'retryFailedJobs')) {
            if ($jobId) {
                return $connection->retryFailedJob($jobId);
            } else {
                $connection->retryFailedJobs();
                return true;
            }
        }
        
        return false;
    }

    /**
     * Create a custom queue job
     *
     * @param callable $closure The job to execute
     * @param array $data Additional data
     * @param string $queue Queue name
     * @return mixed
     */
    public function createCustomJob(callable $closure, array $data = [], string $queue = 'default')
    {
        $job = new CustomQueueJob($closure, $data);
        return Queue::push($job, null, $queue);
    }

    /**
     * Monitor queue performance
     *
     * @return array
     */
    public function monitorQueuePerformance(): array
    {
        $stats = $this->getAllQueueStats();
        
        $performance = [];
        foreach ($stats as $stat) {
            $performance[$stat['queue']] = [
                'current_size' => $stat['size'],
                'estimated_processing_time' => $this->estimateProcessingTime($stat['queue'], $stat['size']),
                'status' => $stat['size'] > 50 ? 'high' : ($stat['size'] > 10 ? 'medium' : 'low'),
            ];
        }
        
        return $performance;
    }

    /**
     * Estimate processing time for a queue
     *
     * @param string $queue Queue name
     * @param int $size Current queue size
     * @return string Estimated time
     */
    private function estimateProcessingTime(string $queue, int $size): string
    {
        // Simple estimation: assume 5 jobs per minute per worker
        if ($size === 0) {
            return '0 minutes';
        }
        
        $jobsPerMinute = 5; // configurable
        $minutes = ceil($size / $jobsPerMinute);
        
        if ($minutes < 60) {
            return "{$minutes} minute(s)";
        } else {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            return "{$hours} hour(s) {$mins} minute(s)";
        }
    }
}

/**
 * Custom Queue Job Class
 */
class CustomQueueJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Dispatchable;

    protected $closure;
    protected $data;

    public function __construct(callable $closure, array $data = [])
    {
        $this->closure = $closure;
        $this->data = $data;
    }

    public function handle()
    {
        try {
            return call_user_func($this->closure, $this->data);
        } catch (\Exception $e) {
            Log::error('Custom queue job failed: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}