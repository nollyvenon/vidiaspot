<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportCategoriesFromJiji;
use App\Jobs\ImportLatestProductsFromJiji;
use App\Models\ProductImportSettings;
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
     * Import latest products from jiji.ng based on configured days
     */
    public function importLatestProducts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days' => 'sometimes|integer|min:1|max:30',
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
            // Get settings or use defaults
            $settings = ProductImportSettings::getCurrentSettings();

            // Override with request parameters if provided
            $days = $request->get('days', $settings->import_days);
            $url = $request->get('url', 'https://jiji.ng');

            // Update settings if needed
            if ($request->has('days')) {
                $settings->import_days = $days;
                $settings->save();
            }

            // Dispatch the job to import latest products
            $job = new ImportLatestProductsFromJiji($days, $url);
            $job->onQueue('import');

            $job->dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Latest products import job dispatched successfully',
                'data' => [
                    'days' => $days,
                    'url' => $url,
                    'job_queued' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error dispatching latest products import job',
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

    /**
     * Get or update import settings
     */
    public function getImportSettings(Request $request): JsonResponse
    {
        $settings = ProductImportSettings::getCurrentSettings();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update import settings from admin panel
     */
    public function updateImportSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'import_days' => 'sometimes|integer|min:1|max:30',
            'import_enabled' => 'sometimes|boolean',
            'import_interval_hours' => 'sometimes|integer|min:1|max:168', // max 1 week
            'import_categories' => 'sometimes|array',
            'import_images' => 'sometimes|boolean',
            'import_location_filter' => 'sometimes|string|max:255',
            'import_price_range_min' => 'sometimes|numeric|min:0',
            'import_price_range_max' => 'sometimes|numeric|min:0',
            'import_duplicate_check' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $settings = ProductImportSettings::getCurrentSettings();

            // Update specified fields
            foreach ($request->only([
                'import_days', 'import_enabled', 'import_interval_hours',
                'import_categories', 'import_images', 'import_location_filter',
                'import_price_range_min', 'import_price_range_max', 'import_duplicate_check'
            ]) as $key => $value) {
                if ($request->has($key)) {
                    $settings->$key = $value;
                }
            }

            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Import settings updated successfully',
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating import settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}