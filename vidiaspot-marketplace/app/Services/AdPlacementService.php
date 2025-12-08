<?php

namespace App\Services;

use App\Models\AdPlacement;
use Illuminate\Support\Facades\Cache;

class AdPlacementService
{
    /**
     * Get active ad placements for a specific position
     * 
     * @param string $position The position where ads should be displayed (top, side, bottom, content_inline, popup, between_content)
     * @param array $filters Additional filters like category, location, etc.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivePlacementsByPosition($position, $filters = [])
    {
        // Try to get from cache first to reduce database reads
        $cacheKey = "ad_placements_{$position}_" . md5(serialize($filters));
        
        return Cache::remember($cacheKey, now()->addHours(1), function() use ($position, $filters) {
            $query = AdPlacement::where('position', $position)
                              ->where('is_active', true)
                              ->where('starts_at', '<=', now())
                              ->where('expires_at', '>=', now());

            // Apply additional filters if provided
            if (!empty($filters['category'])) {
                $query->whereJsonContains('target_audience', $filters['category']);
            }
            
            if (!empty($filters['location'])) {
                $query->whereJsonContains('target_location', $filters['location']);
            }

            return $query->orderBy('priority', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->get();
        });
    }

    /**
     * Get ad placement by ID with caching
     * 
     * @param int $id The placement ID
     * @return \App\Models\AdPlacement|null
     */
    public function getPlacementById($id)
    {
        $cacheKey = "ad_placement_{$id}";
        
        return Cache::remember($cacheKey, now()->addHours(2), function() use ($id) {
            return AdPlacement::find($id);
        });
    }

    /**
     * Get all active ad placements with caching
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActivePlacements()
    {
        $cacheKey = 'all_active_ad_placements';
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function() {
            return AdPlacement::where('is_active', true)
                             ->where('starts_at', '<=', now())
                             ->where('expires_at', '>=', now())
                             ->get();
        });
    }

    /**
     * Clear ad placement cache when placements are updated
     * 
     * @param string|null $position Optional specific position to clear
     * @return void
     */
    public function clearPlacementCache($position = null)
    {
        if ($position) {
            Cache::forget("ad_placements_{$position}_*"); // This would need a more specific implementation
        } else {
            // Clear all ad placement related cache
            Cache::tags(['ad_placements'])->flush();
        }
    }

    /**
     * Get placements by multiple positions
     * 
     * @param array $positions Array of positions
     * @param array $filters Additional filters
     * @return array
     */
    public function getPlacementsByPositions($positions, $filters = [])
    {
        $placements = [];
        
        foreach ($positions as $position) {
            $placements[$position] = $this->getActivePlacementsByPosition($position, $filters);
        }
        
        return $placements;
    }
}