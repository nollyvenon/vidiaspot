<?php

namespace App\Services;

use App\Models\ListingMedia;
use App\Models\ListingAbTest;
use App\Models\InventoryTracking;
use App\Models\ListingOptimizer;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvancedListingService
{
    /**
     * Upload 360-degree product photos
     */
    public function upload360Photos($userId, $adId, $photos, $metadata = [])
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id != $userId) {
            throw new \Exception('Unauthorized to modify this listing');
        }

        $photoUrls = [];
        foreach ($photos as $photo) {
            $path = $photo->store('listing-360-photos/' . $adId, 'public');
            $photoUrls[] = Storage::url($path);
            
            // Create media record
            ListingMedia::create([
                'user_id' => $userId,
                'ad_id' => $adId,
                'media_type' => '360_image',
                'file_path' => $path,
                'file_url' => Storage::url($path),
                'original_filename' => $photo->getClientOriginalName(),
                'media_caption' => $metadata['caption'] ?? '360-degree view',
                'media_alt_text' => $metadata['alt_text'] ?? '360-degree product view',
                'is_primary' => false,
                'display_order' => count($photoUrls) - 1,
                'is_active' => true,
                'media_metadata' => [
                    'size' => $photo->getSize(),
                    'width' => $metadata['width'] ?? null,
                    'height' => $metadata['height'] ?? null,
                    'uploaded_at' => now(),
                    'rotation_angles' => $metadata['angles'] ?? [], // For tracking 360-degree angles
                ],
            ]);
        }

        // Set first photo as primary if no primary exists
        $primaryExists = ListingMedia::where('ad_id', $adId)
                                   ->where('is_primary', true)
                                   ->exists();
        
        if (!$primaryExists) {
            $firstPhoto = ListingMedia::where('ad_id', $adId)
                                    ->where('media_type', '360_image')
                                    ->orderBy('created_at')
                                    ->first();
            
            if ($firstPhoto) {
                $firstPhoto->update(['is_primary' => true]);
            }
        }

        return [
            'success' => true,
            'message' => '360-degree photos uploaded successfully',
            'photo_urls' => $photoUrls,
            'media_records_created' => count($photoUrls),
        ];
    }

    /**
     * Create virtual reality product tour
     */
    public function createVRProductTour($userId, $adId, $tourData)
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id != $userId) {
            throw new \Exception('Unauthorized to modify this listing');
        }

        $media = ListingMedia::create([
            'user_id' => $userId,
            'ad_id' => $adId,
            'media_type' => 'vr_tour',
            'media_caption' => $tourData['title'] ?? 'VR Tour',
            'media_alt_text' => $tourData['description'] ?? 'Virtual reality product tour',
            'is_primary' => false,
            'is_active' => true,
            'media_metadata' => $tourData,
            'custom_fields' => [
                'is_interactive' => $tourData['is_interactive'] ?? true,
                'has_guided_tour' => $tourData['has_guided_tour'] ?? false,
                'hotspot_count' => count($tourData['hotspots'] ?? []),
                'tour_duration_minutes' => $tourData['duration'] ?? 0,
                'vr_compatibility' => $tourData['compatibility'] ?? ['web', 'mobile'],
            ],
        ]);

        return [
            'success' => true,
            'message' => 'VR tour created successfully',
            'vr_tour' => $media,
        ];
    }

    /**
     * Create interactive product demo
     */
    public function createInteractiveDemo($userId, $adId, $demoData)
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id != $userId) {
            throw new \Exception('Unauthorized to modify this listing');
        }

        $demo = ListingMedia::create([
            'user_id' => $userId,
            'ad_id' => $adId,
            'media_type' => 'interactive_demo',
            'media_caption' => $demoData['title'] ?? 'Interactive Demo',
            'media_alt_text' => $demoData['description'] ?? 'Interactive product demonstration',
            'is_primary' => false,
            'is_active' => true,
            'media_metadata' => [
                'components' => $demoData['components'] ?? [],
                'interactivity_level' => $demoData['interactivity_level'] ?? 'medium',
                'features_highlighted' => $demoData['features'] ?? [],
                'demo_type' => $demoData['demo_type'] ?? 'standard',
            ],
            'custom_fields' => [
                'has_zoom' => $demoData['has_zoom'] ?? true,
                'has_rotation' => $demoData['has_rotation'] ?? true,
                'has_specifications' => $demoData['has_specifications'] ?? true,
                'has_comparison' => $demoData['has_comparison'] ?? false,
                'interactions_count' => 0,
            ],
        ]);

        return [
            'success' => true,
            'message' => 'Interactive demo created successfully',
            'demo' => $demo,
        ];
    }

    /**
     * Upload video for listing
     */
    public function uploadVideoForListing($userId, $adId, $video, $metadata = [])
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id != $userId) {
            throw new \Exception('Unauthorized to modify this listing');
        }

        $path = $video->store('listing-videos/' . $adId, 'public');
        $url = Storage::url($path);

        $videoMedia = ListingMedia::create([
            'user_id' => $userId,
            'ad_id' => $adId,
            'media_type' => 'video',
            'file_path' => $path,
            'file_url' => $url,
            'original_filename' => $video->getClientOriginalName(),
            'media_caption' => $metadata['caption'] ?? 'Product video',
            'media_alt_text' => $metadata['alt_text'] ?? 'Video demonstration',
            'is_primary' => false,
            'display_order' => 0, // Videos often prioritized
            'is_active' => true,
            'duration_seconds' => $this->getVideoDuration($video),
            'width_pixels' => $metadata['width'] ?? null,
            'height_pixels' => $metadata['height'] ?? null,
            'media_metadata' => [
                'size' => $video->getSize(),
                'uploaded_at' => now(),
                'video_codec' => $metadata['video_codec'] ?? 'unknown',
                'audio_codec' => $metadata['audio_codec'] ?? 'unknown',
                'bitrate' => $metadata['bitrate'] ?? null,
            ],
        ]);

        return [
            'success' => true,
            'message' => 'Video uploaded successfully',
            'video' => $videoMedia,
        ];
    }

    /**
     * Get live inventory tracking for a listing
     */
    public function getLiveInventory($adId)
    {
        $inventory = InventoryTracking::where('ad_id', $adId)->first();
        
        if (!$inventory) {
            return [
                'success' => false,
                'message' => 'No inventory tracking set up for this listing',
                'has_inventory' => false,
            ];
        }

        return [
            'success' => true,
            'inventory' => [
                'id' => $inventory->id,
                'current_quantity' => $inventory->current_quantity,
                'reserved_quantity' => $inventory->reserved_quantity,
                'available_quantity' => $inventory->current_quantity - $inventory->reserved_quantity,
                'quantity_unit' => $inventory->quantity_unit,
                'low_stock_threshold' => $inventory->low_stock_threshold,
                'reorder_threshold' => $inventory->reorder_threshold,
                'is_low_stock' => $inventory->isLowOnStock(),
                'is_out_of_stock' => $inventory->isOutOfStock(),
                'is_ready_for_reorder' => $inventory->isReadyForReorder(),
                'last_updated' => $inventory->last_updated_at,
                'location_trackable' => $inventory->location_trackable,
                'special_handling' => $inventory->special_handling_available,
                'warehouse_capacity' => $inventory->warehouse_capacity,
            ],
        ];
    }

    /**
     * Update inventory for a listing
     */
    public function updateInventory($adId, $quantityChange, $reason = 'manual_update', $userId = null)
    {
        $inventory = InventoryTracking::firstOrCreate([
            'ad_id' => $adId
        ], [
            'user_id' => $this->getAdUserId($adId), // Get user from ad
            'current_quantity' => 0,
            'reserved_quantity' => 0,
            'sold_quantity' => 0,
            'damaged_quantity' => 0,
            'lost_quantity' => 0,
            'quantity_unit' => 'pieces',
            'low_stock_threshold' => 5,
            'reorder_threshold' => 10,
            'inventory_status' => 'out_of_stock',
        ]);

        $oldQuantity = $inventory->current_quantity;
        $newQuantity = max(0, $oldQuantity + $quantityChange);

        $inventory->update([
            'current_quantity' => $newQuantity,
            'last_updated_by' => $userId,
            'last_updated_at' => now(),
        ]);

        // Record the movement
        $movement = [
            'date' => now(),
            'previous_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'difference' => $quantityChange,
            'reason' => $reason,
            'updated_by' => $userId,
            'timestamp' => now()->toISOString(),
        ];

        $history = $inventory->inventory_history ?? [];
        $history[] = $movement;
        $inventory->update(['inventory_history' => $history]);

        // Update inventory status based on new quantity
        $this->updateInventoryStatus($inventory);

        return [
            'success' => true,
            'message' => 'Inventory updated successfully',
            'inventory' => $inventory,
            'change_log' => $movement,
        ];
    }

    /**
     * Set up automatic listing renewal and optimization
     */
    public function setupAutomaticRenewalAndOptimization($userId, $adId, $settings)
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id != $userId) {
            throw new \Exception('Unauthorized to modify this listing');
        }

        $optimizer = ListingOptimizer::firstOrCreate([
            'user_id' => $userId,
            'ad_id' => $adId,
        ], [
            'optimizer_type' => 'automatic_renewal_optimization',
            'optimization_rules' => $settings['rules'] ?? [],
            'active_schedule' => $settings['schedule'] ?? [],
            'auto_renew_enabled' => $settings['auto_renew'] ?? false,
            'renewal_interval' => $settings['renewal_interval'] ?? 'monthly',
            'renewal_budget' => $settings['renewal_budget'] ?? 0,
            'performance_goals' => $settings['goals'] ?? [],
            'optimization_strategies' => $settings['strategies'] ?? [],
            'last_optimization_run' => now(),
            'next_optimization_run' => $this->getNextOptimizationRunDate($settings['renewal_interval'] ?? 'monthly'),
        ]);

        // Update with new settings if provided
        $optimizer->update([
            'optimization_rules' => $settings['rules'] ?? $optimizer->optimization_rules,
            'active_schedule' => $settings['schedule'] ?? $optimizer->active_schedule,
            'auto_renew_enabled' => $settings['auto_renew'] ?? $optimizer->auto_renew_enabled,
            'renewal_interval' => $settings['renewal_interval'] ?? $optimizer->renewal_interval,
            'renewal_budget' => $settings['renewal_budget'] ?? $optimizer->renewal_budget,
            'performance_goals' => $settings['goals'] ?? $optimizer->performance_goals,
            'optimization_strategies' => $settings['strategies'] ?? $optimizer->optimization_strategies,
            'next_optimization_run' => $this->getNextOptimizationRunDate($settings['renewal_interval'] ?? $optimizer->renewal_interval),
        ]);

        return [
            'success' => true,
            'message' => 'Automatic renewal and optimization set up successfully',
            'optimizer' => $optimizer,
        ];
    }

    /**
     * Run A/B test for listing performance
     */
    public function createListingABTest($userId, $adId, $testConfig)
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id != $userId) {
            throw new \Exception('Unauthorized to modify this listing');
        }

        $test = ListingAbTest::create([
            'user_id' => $userId,
            'ad_id' => $adId,
            'test_name' => $testConfig['test_name'] ?? 'Listing A/B Test',
            'test_description' => $testConfig['test_description'] ?? 'A/B test for listing performance',
            'variant_a_config' => $testConfig['variant_a'] ?? [],
            'variant_b_config' => $testConfig['variant_b'] ?? [],
            'start_date' => now(),
            'end_date' => now()->addDays($testConfig['duration_days'] ?? 7),
            'status' => 'running',
            'traffic_allocation' => $testConfig['traffic_allocation'] ?? ['a' => 50, 'b' => 50],
            'statistical_significance' => $testConfig['statistical_significance'] ?? 95.00,
            'primary_metric' => $testConfig['primary_metric'] ?? 'conversions',
            'secondary_metrics' => $testConfig['secondary_metrics'] ?? [],
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
            'ctr_a' => 0.0000,
            'ctr_b' => 0.0000,
            'revenue_a' => 0.00,
            'revenue_b' => 0.00,
        ]);

        return [
            'success' => true,
            'message' => 'A/B test created successfully',
            'test' => $test,
        ];
    }

    /**
     * Get all advanced media for a listing
     */
    public function getListingAdvancedMedia($adId)
    {
        $media = ListingMedia::where('ad_id', $adId)->orderBy('display_order')->get();
        
        $mediaGroups = [
            'images' => $media->where('media_type', 'image')->values(),
            'videos' => $media->where('media_type', 'video')->values(),
            '360_photos' => $media->where('media_type', '360_image')->values(),
            'vr_tours' => $media->where('media_type', 'vr_tour')->values(),
            'interactive_demos' => $media->where('media_type', 'interactive_demo')->values(),
            'all' => $media,
        ];

        return [
            'success' => true,
            'media_groups' => $mediaGroups,
            'counts' => [
                'total_media' => $media->count(),
                'images' => $mediaGroups['images']->count(),
                'videos' => $mediaGroups['videos']->count(),
                '360_photos' => $mediaGroups['360_photos']->count(),
                'vr_tours' => $mediaGroups['vr_tours']->count(),
                'interactive_demos' => $mediaGroups['interactive_demos']->count(),
            ],
        ];
    }

    /**
     * Book professional photography service
     */
    public function bookProfessionalPhotographyService($userId, $adId, $serviceData)
    {
        $user = User::findOrFail($userId);
        $ad = Ad::findOrFail($adId);
        
        // In a real implementation, this would connect to service providers
        // For simulation, we'll create a booking entry
        $bookingId = 'PHOTO_' . date('Y') . '_' . strtoupper(Str::random(8));
        
        // Add booking details to ad custom fields
        $existingBookings = $ad->custom_fields['photography_bookings'] ?? [];
        $existingBookings[] = [
            'id' => $bookingId,
            'service_type' => $serviceData['service_type'] ?? 'product_photography',
            'scheduled_date' => $serviceData['scheduled_date'],
            'scheduled_time' => $serviceData['scheduled_time'],
            'location' => $serviceData['location'] ?? $ad->location,
            'status' => 'confirmed',
            'service_provider' => $serviceData['service_provider'] ?? 'VidiAspot Photography Partner',
            'notes' => $serviceData['notes'] ?? '',
            'created_at' => now()->toISOString(),
        ];
        
        $ad->update([
            'custom_fields' => array_merge($ad->custom_fields ?? [], ['photography_bookings' => $existingBookings])
        ]);

        return [
            'success' => true,
            'message' => 'Professional photography service booked successfully',
            'booking_id' => $bookingId,
            'booking_details' => end($existingBookings),
        ];
    }

    /**
     * Get listing optimization recommendations
     */
    public function getListingOptimizationRecommendations($adId)
    {
        $ad = Ad::findOrFail($adId);
        
        // This would use AI/ML in a real implementation
        // For simulation, returning sample recommendations
        $recommendations = [
            'title_optimization' => [
                'current_score' => mt_rand(60, 85),
                'improvement_suggestions' => [
                    'Add relevant keywords to title',
                    'Keep title between 60-70 characters',
                    'Include brand name if applicable',
                ],
            ],
            'image_optimization' => [
                'current_score' => mt_rand(45, 90),
                'improvement_suggestions' => [
                    'Add more high-quality images',
                    'Include lifestyle shots',
                    'Consider 360-degree photography',
                    'Ensure good lighting',
                ],
            ],
            'pricing_optimization' => [
                'competitiveness_score' => mt_rand(70, 95),
                'suggestions' => [
                    'Your price is competitive with market',
                    'Consider bundle discounts for multiple items',
                ],
            ],
            'content_optimization' => [
                'current_score' => mt_rand(50, 80),
                'improvement_suggestions' => [
                    'Add more detailed specifications',
                    'Include benefits, not just features',
                    'Add FAQ section',
                ],
            ],
            'seo_optimization' => [
                'current_score' => mt_rand(40, 85),
                'improvement_suggestions' => [
                    'Add more relevant keywords',
                    'Use descriptive alt text',
                    'Include location-specific keywords',
                ],
            ],
        ];

        // Calculate overall optimization score
        $overallScore = array_sum([
            $recommendations['title_optimization']['current_score'],
            $recommendations['image_optimization']['current_score'],
            $recommendations['content_optimization']['current_score'],
            $recommendations['seo_optimization']['current_score'],
        ]) / 4;

        return [
            'success' => true,
            'recommendations' => $recommendations,
            'overall_optimization_score' => round($overallScore, 2),
            'next_optimization_date' => now()->addDays(7)->format('Y-m-d'),
        ];
    }

    /**
     * Private helper methods
     */

    private function getVideoDuration($videoFile)
    {
        // In a real implementation, this would use a library to get actual video duration
        // For simulation, we'll return a random duration between 30 seconds and 5 minutes
        return mt_rand(30, 300);
    }

    private function getAdUserId($adId)
    {
        $ad = Ad::find($adId);
        return $ad ? $ad->user_id : null;
    }

    private function updateInventoryStatus($inventory)
    {
        $quantity = $inventory->current_quantity;
        $threshold = $inventory->low_stock_threshold ?? 5;

        if ($quantity <= 0) {
            $status = 'out_of_stock';
        } elseif ($quantity <= $threshold) {
            $status = 'low_stock';
        } else {
            $status = 'in_stock';
        }

        $inventory->update(['inventory_status' => $status]);
    }

    private function getNextOptimizationRunDate($interval)
    {
        switch ($interval) {
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'monthly':
                return now()->addMonth();
            case 'quarterly':
                return now()->addMonths(3);
            default:
                return now()->addWeek();
        }
    }
}