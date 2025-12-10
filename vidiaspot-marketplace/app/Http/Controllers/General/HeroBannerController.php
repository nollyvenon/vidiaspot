<?php

namespace App\Http\Controllers;

use App\Models\HeroBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeroBannerController extends Controller
{
    /**
     * Get active hero banners for display on the homepage
     */
    public function getActiveBanners()
    {
        $banners = HeroBanner::where('is_active', true)
                            ->where(function($query) {
                                $query->whereNull('start_date')
                                      ->orWhere('start_date', '<=', now());
                            })
                            ->where(function($query) {
                                $query->whereNull('end_date')
                                      ->orWhere('end_date', '>', now());
                            })
                            ->orderBy('position')
                            ->orderBy('created_at', 'asc')
                            ->get();

        // Increment view counts for the banners
        foreach ($banners as $banner) {
            $banner->incrementViewCount();
        }

        return response()->json([
            'success' => true,
            'banners' => $banners,
            'count' => $banners->count()
        ]);
    }

    /**
     * Get featured hero banners only
     */
    public function getFeaturedBanners()
    {
        $banners = HeroBanner::where('is_active', true)
                            ->where('is_featured', true)
                            ->where(function($query) {
                                $query->whereNull('start_date')
                                      ->orWhere('start_date', '<=', now());
                            })
                            ->where(function($query) {
                                $query->whereNull('end_date')
                                      ->orWhere('end_date', '>', now());
                            })
                            ->orderBy('position')
                            ->get();

        return response()->json([
            'success' => true,
            'banners' => $banners,
            'count' => $banners->count()
        ]);
    }

    /**
     * Record a click on a hero banner
     */
    public function recordClick($bannerId)
    {
        $banner = HeroBanner::findOrFail($bannerId);
        $banner->incrementClickCount();

        return response()->json([
            'success' => true,
            'message' => 'Click recorded successfully'
        ]);
    }

    /**
     * Record a conversion from a hero banner
     */
    public function recordConversion($bannerId)
    {
        $banner = HeroBanner::findOrFail($bannerId);
        $banner->incrementConversionCount();

        return response()->json([
            'success' => true,
            'message' => 'Conversion recorded successfully'
        ]);
    }

    /**
     * Get specific banner for preview
     */
    public function getBanner($id)
    {
        $banner = HeroBanner::findOrFail($id);

        // Check if user is admin to view inactive banners
        $user = Auth::user();
        if (!$banner->is_active && (!$user || !$user->hasRole('admin'))) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'banner' => $banner
        ]);
    }

    /**
     * Render the hero banner section on the homepage
     */
    public function renderHeroSection()
    {
        $banners = HeroBanner::where('is_active', true)
                            ->where(function($query) {
                                $query->whereNull('start_date')
                                      ->orWhere('start_date', '<=', now());
                            })
                            ->where(function($query) {
                                $query->whereNull('end_date')
                                      ->orWhere('end_date', '>', now());
                            })
                            ->orderBy('position')
                            ->take(5) // Limit to 5 banners for performance
                            ->get();

        // Prepare the banners for frontend display
        $displayBanners = $banners->map(function($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'description' => $banner->description,
                'button_text' => $banner->button_text,
                'button_url' => $banner->button_url,
                'media_type' => $banner->media_type,
                'media_url' => $banner->media_url, // Includes asset() processing
                'thumbnail_url' => $banner->thumbnail_url, // Includes asset() processing
                'embed_code' => $banner->embed_code, // Includes security sanitization
                'is_featured' => $banner->is_featured,
                'show_timer' => $banner->show_timer,
                'timer_target_date' => $banner->timer_target_date,
                'call_to_action' => $banner->call_to_action,
                'position' => $banner->position,
                'transition_effect' => $banner->transition_effect,
                'animation_duration' => $banner->animation_duration,
                'auto_advance' => $banner->auto_advance,
                'advance_interval' => $banner->advance_interval,
                'show_navigation' => $banner->show_navigation,
                'show_indicators' => $banner->show_indicators,
                'link_target' => $banner->link_target,
                'alt_text' => $banner->alt_text,
                'view_count' => $banner->view_count,
                'click_count' => $banner->click_count,
                'ctr' => $banner->ctr, // Click through rate
                'conversion_rate' => $banner->conversion_rate,
            ];
        });

        return response()->json([
            'success' => true,
            'banners' => $displayBanners,
            'total_banners' => $banners->count()
        ]);
    }
}
