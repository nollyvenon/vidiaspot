<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\ElasticsearchService;

class SearchIndexingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $operation; // 'index', 'update', 'delete'

    public function __construct(array $data, string $operation = 'index')
    {
        $this->data = $data;
        $this->operation = $operation;
    }

    public function handle(ElasticsearchService $elasticsearch)
    {
        try {
            switch ($this->operation) {
                case 'index':
                    $elasticsearch->indexDocument($this->data);
                    break;
                case 'update':
                    $elasticsearch->updateDocument($this->data['id'], $this->data);
                    break;
                case 'delete':
                    $elasticsearch->deleteDocument($this->data['id']);
                    break;
                default:
                    throw new \Exception("Invalid operation: {$this->operation}");
            }

            \Log::info("Search indexing job completed: {$this->operation} with ID: {$this->data['id'] ?? 'unknown'}");
        } catch (\Exception $e) {
            \Log::error("Search indexing job failed: " . $e->getMessage());
            $this->fail($e);
        }
    }
}