<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportCategoriesFromJiji;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CategoryImportController extends Controller
{
    /**
     * Import categories from jiji.ng
     */
    public function importFromJiji(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'sometimes|url|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $url = $request->get('url', 'https://jiji.ng');
            
            // Dispatch the job to import categories
            $job = new ImportCategoriesFromJiji($url);
            $job->onQueue('import'); // Use a specific queue for imports
            
            // For immediate processing
            $job->dispatch();
            
            // Or dispatch to queue for background processing
            // ImportCategoriesFromJiji::dispatch($url)->onQueue('import');
            
            return response()->json([
                'success' => true,
                'message' => 'Category import job dispatched successfully',
                'data' => [
                    'url' => $url,
                    'job_queued' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error dispatching category import job',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get import status or statistics
     */
    public function importStatus(Request $request): JsonResponse
    {
        // In a real implementation, this would check the status of import jobs
        // For now, returning a placeholder response
        
        return response()->json([
            'success' => true,
            'data' => [
                'import_jobs_count' => 0,
                'completed_imports' => 0,
                'pending_imports' => 0,
                'last_import_time' => null
            ]
        ]);
    }
}