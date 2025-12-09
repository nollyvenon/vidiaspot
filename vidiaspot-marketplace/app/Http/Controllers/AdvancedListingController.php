<?php

namespace App\Http\Controllers;

use App\Services\TrustSafetyService;
use App\Services\AdvancedListingService;
use App\Models\ListingMedia;
use App\Models\ListingAbTest;
use App\Models\InventoryTracking;
use App\Models\ListingOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvancedListingController extends Controller
{
    protected $trustSafetyService;
    protected $advancedListingService;

    public function __construct(TrustSafetyService $trustSafetyService, AdvancedListingService $advancedListingService)
    {
        $this->trustSafetyService = $trustSafetyService;
        $this->advancedListingService = $advancedListingService;
    }

    /**
     * Upload 360-degree photos for a listing
     */
    public function upload360Photos(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'photos' => 'required|array|min:6|max:36', // At least 6 and max 36 photos for 360 view
            'photos.*' => 'file|mimes:jpeg,jpg,png,webp|max:10240', // Max 10MB per photo
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $result = $this->advancedListingService->upload360Photos(
                $user->id,
                $request->ad_id,
                $request->file('photos'),
                [
                    'caption' => $request->title,
                    'alt_text' => $request->description,
                    'angles' => range(0, 330, 360 / count($request->file('photos'))), // Distribute angles
                ]
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload 360 photos: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Upload video for a listing
     */
    public function uploadVideo(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'video' => 'required|file|mimetypes:video/mp4,video/avi,video/mov,video/wmv|max:51200', // Max 50MB
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_preview' => 'boolean',
        ]);

        $videoPath = $request->file('video')->store('listing-videos', 'public');

        $mediaItem = ListingMedia::create([
            'user_id' => $user->id,
            'ad_id' => $request->ad_id,
            'media_type' => 'video',
            'file_path' => $videoPath,
            'file_url' => Storage::url($videoPath),
            'original_filename' => $request->file('video')->getClientOriginalName(),
            'media_caption' => $request->title,
            'media_alt_text' => $request->description,
            'is_primary' => $request->boolean('is_preview'),
            'is_active' => true,
            'duration_seconds' => $this->getVideoDuration($request->file('video')),
            'media_metadata' => [
                'size' => $request->file('video')->getSize(),
                'uploaded_at' => now(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video uploaded successfully',
            'media_item' => $mediaItem
        ]);
    }

    /**
     * Create VR tour experience for a listing
     */
    public function createVRTour(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'tour_data' => 'required|array', // Contains 360 images and navigation data
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_interactive' => 'boolean',
            'virtual_guide' => 'nullable|boolean',
        ]);

        // VR tour would typically be created as a collection of 360 photos with navigation data
        $vrTour = ListingMedia::create([
            'user_id' => $user->id,
            'ad_id' => $request->ad_id,
            'media_type' => 'vr_tour',
            'media_caption' => $request->title,
            'media_alt_text' => $request->description,
            'is_primary' => true,
            'is_active' => true,
            'media_metadata' => $request->tour_data,
            'custom_fields' => [
                'is_interactive' => $request->boolean('is_interactive'),
                'has_virtual_guide' => $request->boolean('virtual_guide'),
                'hotspots_count' => count($request->tour_data['hotspots'] ?? []),
                'duration_minutes' => $request->tour_data['duration'] ?? 0,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'VR tour created successfully',
            'vr_tour' => $vrTour
        ]);
    }

    /**
     * Create interactive product demo
     */
    public function createInteractiveDemo(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'demo_data' => 'required|array', // Contains interactive elements and hotspots
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'demo_type' => 'in:product_showcase,feature_demo,tutorial,configuration_tool',
        ]);

        // Interactive demo would typically use 3D models or interactive media
        $demo = ListingMedia::create([
            'user_id' => $user->id,
            'ad_id' => $request->ad_id,
            'media_type' => 'interactive_demo',
            'media_caption' => $request->title,
            'media_alt_text' => $request->description,
            'is_primary' => false,
            'is_active' => true,
            'media_metadata' => $request->demo_data,
            'custom_fields' => [
                'demo_type' => $request->demo_type,
                'elements_count' => count($request->demo_data['elements'] ?? []),
                'interactivity_level' => $request->demo_data['interactivity_level'] ?? 'medium',
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Interactive demo created successfully',
            'demo' => $demo
        ]);
    }

    /**
     * Get live inventory tracking for a listing
     */
    public function getLiveInventory(Request $request, $adId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $inventory = InventoryTracking::where('ad_id', $adId)
                                    ->where('user_id', $user->id)
                                    ->first();

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'No inventory tracking found for this listing'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'inventory' => [
                'current_quantity' => $inventory->current_quantity,
                'reserved_quantity' => $inventory->reserved_quantity,
                'available_quantity' => $inventory->current_quantity - $inventory->reserved_quantity,
                'quantity_unit' => $inventory->quantity_unit,
                'last_updated' => $inventory->last_updated_at,
                'location_trackable' => $inventory->location_trackable,
                'last_scanned_at' => $inventory->last_scanned_at,
                'low_stock_threshold' => $inventory->low_stock_threshold,
                'reorder_threshold' => $inventory->reorder_threshold,
                'is_low_stock' => $inventory->isLowOnStock(),
                'is_out_of_stock' => $inventory->isOutOfStock(),
                'is_ready_for_reorder' => $inventory->isReadyForReorder(),
            ]
        ]);
    }

    /**
     * Update inventory tracking
     */
    public function updateInventory(Request $request, $adId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'quantity_change' => 'required|integer', // Can be negative to decrease stock
            'reason' => 'required|string|in:received_stock,sale,return,damage,loss,adjustment',
            'transaction_note' => 'nullable|string'
        ]);

        $inventory = InventoryTracking::firstOrCreate([
            'user_id' => $user->id,
            'ad_id' => $adId
        ], [
            'current_quantity' => 0,
            'reserved_quantity' => 0,
            'sold_quantity' => 0,
            'damaged_quantity' => 0,
            'lost_quantity' => 0,
            'quantity_unit' => 'pieces',
            'low_stock_threshold' => 5,
            'reorder_threshold' => 10,
            'inventory_status' => 'in_stock',
        ]);

        $newQuantity = max(0, $inventory->current_quantity + $request->quantity_change);

        // Record the movement
        $inventory->recordMovement(
            abs($request->quantity_change),
            $request->reason,
            $request->transaction_note ?? 'Inventory update',
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Inventory updated successfully',
            'inventory' => $inventory
        ]);
    }

    /**
     * Set up automatic listing renewal and optimization
     */
    public function setupAutoRenewOptimization(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'renewal_interval' => 'required|in:daily,weekly,bimonthly,monthly,quarterly',
            'renewal_budget' => 'required|numeric|min:0',
            'enable_performance_optimization' => 'boolean',
            'automated_actions' => 'array',
            'pricing_optimization_enabled' => 'boolean',
            'content_optimization_enabled' => 'boolean',
            'timing_optimization_enabled' => 'boolean',
        ]);

        $optimizer = ListingOptimizer::create([
            'user_id' => $user->id,
            'ad_id' => $request->ad_id,
            'optimizer_type' => 'performance_optimization',
            'optimizer_config' => [
                'renewal_enabled' => true,
                'renewal_interval' => $request->renewal_interval,
                'renewal_budget' => $request->renewal_budget,
                'performance_optimization' => $request->enable_performance_optimization,
                'pricing_optimization' => $request->pricing_optimization_enabled,
                'content_optimization' => $request->content_optimization_enabled,
                'timing_optimization' => $request->timing_optimization_enabled,
            ],
            'optimization_rules' => $request->automated_actions,
            'auto_renew_enabled' => true,
            'renewal_interval' => $request->renewal_interval,
            'renewal_budget' => $request->renewal_budget,
            'last_optimization_run' => now(),
            'next_optimization_run' => $this->calculateNextRunDate($request->renewal_interval),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Auto renewal and optimization set up successfully',
            'optimizer' => $optimizer
        ]);
    }

    /**
     * Run listing optimization
     */
    public function runOptimization($optimizerId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $optimizer = ListingOptimizer::where('id', $optimizerId)
                                   ->where('user_id', $user->id)
                                   ->firstOrFail();

        // In a real implementation, this would run various optimization algorithms
        // For now, we'll simulate the optimization process
        $optimizationResults = [
            'title_optimization' => [
                'original_score' => mt_rand(60, 80),
                'optimized_score' => mt_rand(85, 95),
                'suggestions' => ['Add more keywords', 'Make title more compelling'],
            ],
            'image_optimization' => [
                'quality_score' => mt_rand(70, 90),
                'suggestions' => ['Add more angles', 'Improve lighting'],
            ],
            'pricing_optimization' => [
                'competitiveness_score' => mt_rand(75, 95),
                'recommended_price' => $this->calculateOptimalPrice($optimizer->ad_id)
            ],
            'content_optimization' => [
                'description_score' => mt_rand(65, 85),
                'suggestions' => ['Add more details', 'Include specifications'],
            ],
        ];

        // Update optimizer with results
        $optimizer->update([
            'optimization_score' => $this->calculateOptimizationScore($optimizationResults),
            'last_optimization_run' => now(),
            'next_optimization_run' => $this->calculateNextRunDate('weekly'), // Default weekly
            'optimization_history' => array_merge(
                $optimizer->optimization_history ?? [],
                [array_merge($optimizationResults, ['run_date' => now()])]
            )
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Optimization run completed successfully',
            'results' => $optimizationResults,
            'optimizer' => $optimizer
        ]);
    }

    /**
     * Create A/B test for listing performance
     */
    public function createABTest(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'test_name' => 'required|string|max:255',
            'test_description' => 'nullable|string',
            'variant_a_config' => 'required|array',
            'variant_b_config' => 'required|array',
            'traffic_allocation' => 'array',
            'traffic_allocation.a' => 'required|integer|min:0|max:100',
            'traffic_allocation.b' => 'required|integer|min:0|max:100|same:' . (100 - ($request->input('traffic_allocation.a') ?? 0)),
            'primary_metric' => 'required|in:conversions,clicks,engagement,time_on_page',
            'test_duration_days' => 'required|integer|min:3|max:30',
        ]);

        $test = ListingAbTest::create([
            'user_id' => $user->id,
            'ad_id' => $request->ad_id,
            'test_name' => $request->test_name,
            'test_description' => $request->test_description,
            'variant_a_config' => $request->variant_a_config,
            'variant_b_config' => $request->variant_b_config,
            'start_date' => now(),
            'end_date' => now()->addDays($request->test_duration_days),
            'status' => 'running',
            'traffic_allocation' => $request->traffic_allocation,
            'primary_metric' => $request->primary_metric,
            'secondary_metrics' => $request->secondary_metrics ?? [],
            'statistical_significance' => 95.00, // Default 95%
            'sample_size_a' => 0,
            'sample_size_b' => 0,
            'conversion_rate_a' => 0.0000,
            'conversion_rate_b' => 0.0000,
            'impression_count_a' => 0,
            'impression_count_b' => 0,
            'click_count_a' => 0,
            'click_count_b' => 0,
            'engagement_rate_a' => 0.0000,
            'engagement_rate_b' => 0.0000,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'A/B test created successfully',
            'test' => $test
        ]);
    }

    /**
     * Get A/B test results
     */
    public function getABTestResults($testId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $test = ListingAbTest::where('id', $testId)
                           ->where('user_id', $user->id)
                           ->firstOrFail();

        // Calculate derived metrics
        $test->load('ad');

        $results = [
            'test_info' => [
                'id' => $test->id,
                'name' => $test->test_name,
                'status' => $test->status,
                'start_date' => $test->start_date,
                'end_date' => $test->end_date,
                'duration_days' => $test->start_date->diffInDays($test->end_date),
            ],
            'variant_a' => [
                'config' => $test->variant_a_config,
                'impressions' => $test->impression_count_a,
                'clicks' => $test->click_count_a,
                'conversions' => $test->sample_size_a,
                'conversion_rate' => $test->conversion_rate_a,
                'engagement_rate' => $test->engagement_rate_a,
            ],
            'variant_b' => [
                'config' => $test->variant_b_config,
                'impressions' => $test->impression_count_b,
                'clicks' => $test->click_count_b,
                'conversions' => $test->sample_size_b,
                'conversion_rate' => $test->conversion_rate_b,
                'engagement_rate' => $test->engagement_rate_b,
            ],
            'comparison' => [
                'winning_variant' => $this->determineWinner($test),
                'lift_percentage' => $test->getLiftPercentage(),
                'significance' => $test->hasStatisticalSignificance(),
                'confidence' => $test->confidence_level,
            ],
            'recommendations' => $this->generateRecommendations($test)
        ];

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Book professional photography service
     */
    public function bookPhotographyService(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'service_type' => 'required|in:product_photography,360_shots,professional_videos,studio_shoot,commercial_photography',
            'location' => 'required|array',
            'location.address' => 'required|string',
            'location.city' => 'required|string',
            'location.state' => 'required|string',
            'location.country' => 'required|string',
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|date_format:H:i',
            'special_requirements' => 'array',
            'preferences' => 'array',
        ]);

        // In a real implementation, this would connect to photography service providers
        $bookingReference = 'PHOTO_' . date('Y') . '_' . Str::upper(Str::random(6));

        // For simulation, create a booking record in custom fields
        $ad = \App\Models\Ad::find($request->ad_id);
        $bookings = $ad->custom_fields['photography_bookings'] ?? [];
        $bookings[] = [
            'reference' => $bookingReference,
            'service_type' => $request->service_type,
            'location' => $request->location,
            'scheduled_datetime' => $request->scheduled_date . ' ' . $request->scheduled_time,
            'requirements' => $request->special_requirements,
            'preferences' => $request->preferences,
            'status' => 'confirmed',
            'created_at' => now(),
        ];

        $ad->update(['custom_fields' => array_merge($ad->custom_fields ?? [], ['photography_bookings' => $bookings])]);

        return response()->json([
            'success' => true,
            'message' => 'Photography service booked successfully',
            'booking_reference' => $bookingReference,
            'booking_details' => end($bookings)
        ]);
    }

    /**
     * Get advanced listing features for an ad
     */
    public function getAdvancedListingFeatures($adId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $ad = \App\Models\Ad::where('id', $adId)
                          ->where('user_id', $user->id)
                          ->first();

        if (!$ad) {
            return response()->json([
                'success' => false,
                'message' => 'Ad not found or unauthorized'
            ], 404);
        }

        $media = ListingMedia::where('ad_id', $adId)->orderBy('display_order')->get();
        $optimizer = ListingOptimizer::where('ad_id', $adId)->where('user_id', $user->id)->first();
        $abTests = ListingAbTest::where('ad_id', $adId)->where('user_id', $user->id)->get();
        $inventory = InventoryTracking::where('ad_id', $adId)->where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'features' => [
                'media_gallery' => [
                    'total_media' => $media->count(),
                    '360_photos' => $media->where('media_type', '360_image')->count(),
                    'videos' => $media->where('media_type', 'video')->count(),
                    'vr_tours' => $media->where('media_type', 'vr_tour')->count(),
                    'interactive_demos' => $media->where('media_type', 'interactive_demo')->count(),
                    'main_media' => $media->firstWhere('is_primary'),
                    'all_media' => $media,
                ],
                'inventory_tracking' => $inventory ? [
                    'current_quantity' => $inventory->current_quantity,
                    'available_quantity' => $inventory->current_quantity - $inventory->reserved_quantity,
                    'status' => $inventory->inventory_status,
                    'is_low_stock' => $inventory->isLowOnStock(),
                    'is_out_of_stock' => $inventory->isOutOfStock(),
                ] : null,
                'optimization' => $optimizer ? [
                    'is_active' => $optimizer->auto_renew_enabled,
                    'renewal_interval' => $optimizer->renewal_interval,
                    'optimization_score' => $optimizer->optimization_score,
                    'last_run' => $optimizer->last_optimization_run,
                    'next_run' => $optimizer->next_optimization_run,
                ] : null,
                'ab_testing' => [
                    'active_tests' => $abTests->count(),
                    'tests' => $abTests,
                ],
                'photography_services' => $ad->custom_fields['photography_bookings'] ?? [],
            ]
        ]);
    }

    /**
     * Helper methods
     */

    private function getImageDimensions($file)
    {
        // In a real implementation, we'd get the actual dimensions
        // For simulation return dummy values
        return [mt_rand(1000, 4000), mt_rand(800, 3000)];
    }

    private function getVideoDuration($file)
    {
        // In a real implementation, we'd use a library to get video duration
        // For simulation return random duration between 30 seconds and 5 minutes
        return mt_rand(30, 300);
    }

    private function calculateNextRunDate($interval)
    {
        switch ($interval) {
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'bimonthly':
                return now()->addDays(15);
            case 'monthly':
                return now()->addMonth();
            case 'quarterly':
                return now()->addMonths(3);
            default:
                return now()->addWeek();
        }
    }

    private function calculateOptimalPrice($adId)
    {
        // In a real implementation, this would analyze market data
        // For simulation return a price near current price
        $ad = \App\Models\Ad::find($adId);
        if (!$ad) return 0;

        // Return a price 5-15% different from current
        $factor = mt_rand(85, 115) / 100;
        return $ad->price * $factor;
    }

    private function calculateOptimizationScore($results)
    {
        // Calculate an overall score based on optimization results
        $total = 0;
        $count = 0;

        if (isset($results['title_optimization']['optimized_score'])) {
            $total += $results['title_optimization']['optimized_score'];
            $count++;
        }

        if (isset($results['image_optimization']['quality_score'])) {
            $total += $results['image_optimization']['quality_score'];
            $count++;
        }

        if (isset($results['content_optimization']['description_score'])) {
            $total += $results['content_optimization']['description_score'];
            $count++;
        }

        return $count > 0 ? round($total / $count, 2) : 0;
    }

    private function determineWinner($test)
    {
        // Determine winner based on the primary metric
        switch ($test->primary_metric) {
            case 'conversions':
                return $test->conversion_rate_a >= $test->conversion_rate_b ? 'A' : 'B';
            case 'clicks':
                return $test->click_count_a >= $test->click_count_b ? 'A' : 'B';
            case 'engagement':
                return $test->engagement_rate_a >= $test->engagement_rate_b ? 'A' : 'B';
            case 'time_on_page':
                return $test->time_on_page_a >= $test->time_on_page_b ? 'A' : 'B';
            default:
                return $test->conversion_rate_a >= $test->conversion_rate_b ? 'A' : 'B';
        }
    }

    private function generateRecommendations($test)
    {
        // Generate recommendations based on test results
        $recommendations = [];

        if ($this->determineWinner($test) === 'A') {
            $recommendations[] = "Variant A performed better. Consider implementing the changes from Variant A.";
        } else {
            $recommendations[] = "Variant B performed better. Consider implementing the changes from Variant B.";
        }

        if ($test->hasStatisticalSignificance()) {
            $recommendations[] = "Results are statistically significant, so the winning variant is reliable.";
        } else {
            $recommendations[] = "Continue running the test to achieve statistical significance.";
        }

        return $recommendations;
    }
}
