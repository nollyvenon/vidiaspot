<?php

namespace App\Services;

use App\Models\VirtualShowroom;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MetaverseService
{
    public function createVirtualShowroom($showroomData, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return VirtualShowroom::create([
            'name' => $showroomData['name'],
            'description' => $showroomData['description'] ?? '',
            'slug' => \Str::slug($showroomData['name']),
            'owner_id' => $userId,
            'vendor_id' => $showroomData['vendor_id'] ?? null,
            'platform' => $showroomData['platform'] ?? 'custom',
            'url' => $showroomData['url'] ?? null,
            'embed_code' => $showroomData['embed_code'] ?? null,
            'thumbnail_url' => $showroomData['thumbnail_url'] ?? null,
            'background_image_url' => $showroomData['background_image_url'] ?? null,
            'virtual_environment' => $showroomData['virtual_environment'] ?? 'standard',
            'is_public' => $showroomData['is_public'] ?? false,
            'is_active' => $showroomData['is_active'] ?? true,
            'max_visitors' => $showroomData['max_visitors'] ?? 100,
            'current_visitors' => 0,
            'requires_reservation' => $showroomData['requires_reservation'] ?? false,
            'reservation_fee' => $showroomData['reservation_fee'] ?? 0,
            'currency' => $showroomData['currency'] ?? 'USD',
            'start_date' => $showroomData['start_date'] ?? null,
            'end_date' => $showroomData['end_date'] ?? null,
            'opening_hours' => $showroomData['opening_hours'] ?? [],
            'features' => $showroomData['features'] ?? [],
            'settings' => $showroomData['settings'] ?? [],
            'metadata' => $showroomData['metadata'] ?? [],
        ]);
    }

    public function updateShowroom($showroomId, $showroomData, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $showroom = VirtualShowroom::where('id', $showroomId)
            ->where('owner_id', $userId)
            ->first();

        if ($showroom) {
            $showroom->update($showroomData);
        }

        return $showroom;
    }

    public function addProductToShowroom($showroomId, $productId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $showroom = VirtualShowroom::where('id', $showroomId)
            ->where('owner_id', $userId)
            ->first();

        if ($showroom) {
            $showroom->products()->attach($productId);
        }

        return $showroom;
    }

    public function removeProductFromShowroom($showroomId, $productId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $showroom = VirtualShowroom::where('id', $showroomId)
            ->where('owner_id', $userId)
            ->first();

        if ($showroom) {
            $showroom->products()->detach($productId);
        }

        return $showroom;
    }

    public function getShowroomProducts($showroomId)
    {
        $showroom = VirtualShowroom::with(['products' => function($query) {
            $query->with(['user', 'category', 'images']);
        }])->find($showroomId);

        return $showroom ? $showroom->products : collect();
    }

    public function joinShowroom($showroomId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $showroom = VirtualShowroom::find($showroomId);

        if ($showroom && $showroom->is_active) {
            // Increment current visitors count
            $showroom->increment('current_visitors');

            // Record the visit
            $showroom->visitors()->attach($userId, [
                'visit_time' => now(),
                'duration' => 0,
                'status' => 'active'
            ]);

            return $showroom;
        }

        return null;
    }

    public function leaveShowroom($showroomId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $showroom = VirtualShowroom::find($showroomId);

        if ($showroom) {
            // Decrement current visitors count if greater than 0
            if ($showroom->current_visitors > 0) {
                $showroom->decrement('current_visitors');
            }

            // Update visit record
            $pivot = $showroom->visitors()->where('user_id', $userId)->first();
            if ($pivot) {
                $visit = $showroom->visitors()->wherePivot('user_id', $userId)->first();
                
                // Calculate duration
                $visitTime = $showroom->visitors()->wherePivot('user_id', $userId)
                    ->first()->pivot->visit_time;
                $duration = now()->diffInMinutes($visitTime);
                
                $showroom->visitors()->updateExistingPivot($userId, [
                    'duration' => $duration,
                    'status' => 'completed'
                ]);
            }

            return $showroom;
        }

        return null;
    }

    public function getActiveShowrooms($filters = [])
    {
        $query = VirtualShowroom::active()->with('owner', 'products')->orderBy('name');

        if (isset($filters['platform'])) {
            $query->forPlatform($filters['platform']);
        }

        if (isset($filters['is_public'])) {
            $query->public();
        }

        if (isset($filters['owner_id'])) {
            $query->where('owner_id', $filters['owner_id']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getShowroomById($showroomId)
    {
        return VirtualShowroom::with(['owner', 'vendor', 'products', 'visitors'])->find($showroomId);
    }

    public function getShowroomByUrl($url)
    {
        return VirtualShowroom::where('url', $url)->first();
    }

    public function getShowroomBySlug($slug)
    {
        return VirtualShowroom::where('slug', $slug)->first();
    }

    public function getTrendingShowrooms($limit = 10)
    {
        // This would typically use analytics data to determine trending showrooms
        // For now, we'll return showrooms ordered by visitor count
        return VirtualShowroom::active()
            ->orderBy('current_visitors', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getFeaturedShowrooms($limit = 5)
    {
        return VirtualShowroom::active()
            ->public()
            ->where('is_active', true)
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}