<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroBannersController extends Controller
{
    /**
     * Display a listing of hero banners.
     */
    public function index(Request $request)
    {
        $query = HeroBanner::query();

        // Apply filters
        if ($request->has('is_active')) {
            $query = $query->where('is_active', $request->is_active);
        }

        if ($request->has('is_featured')) {
            $query = $query->where('is_featured', $request->is_featured);
        }

        if ($request->has('media_type')) {
            $query = $query->where('media_type', $request->media_type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query = $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('subtitle', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $banners = $query->orderBy('position')
                        ->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'banners' => $banners
        ]);
    }

    /**
     * Store a newly created hero banner.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'media_type' => 'required|in:image,video,video_embed,carousel,mixed',
            'media_url' => 'nullable|url',
            'thumbnail_url' => 'nullable|url',
            'embed_code' => 'nullable|string', // For embedded videos
            'position' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'show_timer' => 'boolean',
            'timer_target_date' => 'nullable|date',
            'call_to_action' => 'nullable|string|max:255',
            'target_audience' => 'array',
            'target_audience.*' => 'string|in:buyers,sellers,general,all',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'display_conditions' => 'array',
            'transition_effect' => 'in:fade,slide,zoom,none',
            'animation_duration' => 'integer|min:100|max:5000',
            'advance_interval' => 'integer|min:1|max:60', // 1-60 seconds
            'auto_advance' => 'boolean',
            'show_navigation' => 'boolean',
            'show_indicators' => 'boolean',
            'link_target' => 'in:_self,_blank',
            'alt_text' => 'nullable|string|max:500',
            'seo_keywords' => 'array',
            'seo_keywords.*' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle uploaded media if provided
        $mediaUrl = null;
        $thumbnailUrl = null;

        if ($request->hasFile('media_file')) {
            $mediaFile = $request->file('media_file');
            $mediaExtension = $mediaFile->extension();
            $mediaDir = in_array($mediaExtension, ['mp4', 'mov', 'avi']) ? 'hero-banners/videos' : 'hero-banners/images';

            $mediaUrl = $mediaFile->store($mediaDir, 'public');
        }

        if ($request->hasFile('thumbnail_file')) {
            $thumbnailFile = $request->file('thumbnail_file');
            $thumbnailUrl = $thumbnailFile->store('hero-banners/thumbnails', 'public');
        }

        $banner = HeroBanner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'button_text' => $request->button_text ?? 'Learn More',
            'button_url' => $request->button_url,
            'media_type' => $request->media_type,
            'media_url' => $mediaUrl ? Storage::url($mediaUrl) : $request->media_url,
            'thumbnail_url' => $thumbnailUrl ? Storage::url($thumbnailUrl) : $request->thumbnail_url,
            'embed_code' => $request->embed_code,
            'position' => $request->position,
            'is_active' => $request->is_active ?? false,
            'is_featured' => $request->is_featured ?? false,
            'show_timer' => $request->show_timer ?? false,
            'timer_target_date' => $request->timer_target_date,
            'call_to_action' => $request->call_to_action,
            'target_audience' => $request->target_audience ?? [],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'display_conditions' => $request->display_conditions ?? [],
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'custom_css_classes' => $request->custom_css_classes ?? [],
            'transition_effect' => $request->transition_effect ?? 'fade',
            'animation_duration' => $request->animation_duration ?? 500,
            'auto_advance' => $request->auto_advance ?? true,
            'advance_interval' => $request->advance_interval ?? 5,
            'show_navigation' => $request->show_navigation ?? true,
            'show_indicators' => $request->show_indicators ?? true,
            'link_target' => $request->link_target ?? '_self',
            'alt_text' => $request->alt_text,
            'seo_keywords' => $request->seo_keywords ?? [],
            'created_by' => auth()->id(),
            'custom_fields' => $request->custom_fields ?? [],
            'metadata' => $request->metadata ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hero banner created successfully',
            'banner' => $banner
        ], 201);
    }

    /**
     * Display the specified hero banner.
     */
    public function show($id)
    {
        $banner = HeroBanner::findOrFail($id);

        return response()->json([
            'success' => true,
            'banner' => $banner
        ]);
    }

    /**
     * Update the specified hero banner.
     */
    public function update(Request $request, $id)
    {
        $banner = HeroBanner::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'media_type' => 'required|in:image,video,video_embed,carousel,mixed',
            'media_url' => 'nullable|url',
            'thumbnail_url' => 'nullable|url',
            'embed_code' => 'nullable|string',
            'position' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'show_timer' => 'boolean',
            'timer_target_date' => 'nullable|date',
            'call_to_action' => 'nullable|string|max:255',
            'target_audience' => 'array',
            'target_audience.*' => 'string|in:buyers,sellers,general,all',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'display_conditions' => 'array',
            'transition_effect' => 'in:fade,slide,zoom,none',
            'animation_duration' => 'integer|min:100|max:5000',
            'advance_interval' => 'integer|min:1|max:60',
            'auto_advance' => 'boolean',
            'show_navigation' => 'boolean',
            'show_indicators' => 'boolean',
            'link_target' => 'in:_self,_blank',
            'alt_text' => 'nullable|string|max:500',
            'seo_keywords' => 'array',
            'seo_keywords.*' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle uploaded media if provided
        $mediaUrl = $banner->media_url;
        $thumbnailUrl = $banner->thumbnail_url;

        if ($request->hasFile('media_file')) {
            // Delete old media if it was stored in our system
            if ($banner->media_url && Str::startsWith($banner->media_url, Storage::url(''))) {
                $oldPath = str_replace(Storage::url(''), '', $banner->media_url);
                Storage::disk('public')->delete($oldPath);
            }

            $mediaFile = $request->file('media_file');
            $mediaExtension = $mediaFile->extension();
            $mediaDir = in_array($mediaExtension, ['mp4', 'mov', 'avi']) ? 'hero-banners/videos' : 'hero-banners/images';

            $mediaUrl = $mediaFile->store($mediaDir, 'public');
            $mediaUrl = Storage::url($mediaUrl);
        }

        if ($request->hasFile('thumbnail_file')) {
            // Delete old thumbnail if it was stored in our system
            if ($banner->thumbnail_url && Str::startsWith($banner->thumbnail_url, Storage::url(''))) {
                $oldPath = str_replace(Storage::url(''), '', $banner->thumbnail_url);
                Storage::disk('public')->delete($oldPath);
            }

            $thumbnailFile = $request->file('thumbnail_file');
            $thumbnailUrl = $thumbnailFile->store('hero-banners/thumbnails', 'public');
            $thumbnailUrl = Storage::url($thumbnailUrl);
        }

        $banner->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'media_type' => $request->media_type,
            'media_url' => $mediaUrl,
            'thumbnail_url' => $thumbnailUrl,
            'embed_code' => $request->embed_code,
            'position' => $request->position,
            'is_active' => $request->is_active ?? $banner->is_active,
            'is_featured' => $request->is_featured ?? $banner->is_featured,
            'show_timer' => $request->show_timer ?? $banner->show_timer,
            'timer_target_date' => $request->timer_target_date,
            'call_to_action' => $request->call_to_action,
            'target_audience' => $request->target_audience ?? [],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'display_conditions' => $request->display_conditions ?? [],
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'custom_css_classes' => $request->custom_css_classes ?? [],
            'transition_effect' => $request->transition_effect ?? $banner->transition_effect,
            'animation_duration' => $request->animation_duration ?? $banner->animation_duration,
            'auto_advance' => $request->auto_advance ?? $banner->auto_advance,
            'advance_interval' => $request->advance_interval ?? $banner->advance_interval,
            'show_navigation' => $request->show_navigation ?? $banner->show_navigation,
            'show_indicators' => $request->show_indicators ?? $banner->show_indicators,
            'link_target' => $request->link_target ?? $banner->link_target,
            'alt_text' => $request->alt_text,
            'seo_keywords' => $request->seo_keywords ?? [],
            'updated_by' => auth()->id(),
            'custom_fields' => $request->custom_fields ?? [],
            'metadata' => $request->metadata ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hero banner updated successfully',
            'banner' => $banner
        ]);
    }

    /**
     * Remove the specified hero banner.
     */
    public function destroy($id)
    {
        $banner = HeroBanner::findOrFail($id);

        // Delete media files if they exist
        if ($banner->media_url && Str::startsWith($banner->media_url, Storage::url(''))) {
            $mediaPath = str_replace(Storage::url(''), '', $banner->media_url);
            Storage::disk('public')->delete($mediaPath);
        }

        if ($banner->thumbnail_url && Str::startsWith($banner->thumbnail_url, Storage::url(''))) {
            $thumbnailPath = str_replace(Storage::url(''), '', $banner->thumbnail_url);
            Storage::disk('public')->delete($thumbnailPath);
        }

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hero banner deleted successfully'
        ]);
    }

    /**
     * Toggle the active status of a hero banner.
     */
    public function toggleStatus($id)
    {
        $banner = HeroBanner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Banner status updated successfully',
            'banner' => $banner
        ]);
    }

    /**
     * Get active hero banners for the frontend.
     */
    public function getActiveBanners()
    {
        $now = now();

        $banners = HeroBanner::where('is_active', true)
                           ->where(function($q) use ($now) {
                               $q->whereNull('start_date')
                                 ->orWhere('start_date', '<=', $now);
                           })
                           ->where(function($q) use ($now) {
                               $q->whereNull('end_date')
                                 ->orWhere('end_date', '>', $now);
                           })
                           ->orderBy('position')
                           ->orderBy('created_at', 'asc')
                           ->get();

        return response()->json([
            'success' => true,
            'banners' => $banners
        ]);
    }

    /**
     * Reorder banners by position.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|integer|exists:hero_banners,id',
            'banners.*.position' => 'required|integer|min:0',
        ]);

        foreach ($request->banners as $bannerData) {
            HeroBanner::where('id', $bannerData['id'])
                     ->update(['position' => $bannerData['position']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banners reordered successfully',
            'banners' => HeroBanner::orderBy('position')->get()
        ]);
    }
}
