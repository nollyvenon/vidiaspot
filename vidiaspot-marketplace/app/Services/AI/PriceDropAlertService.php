<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use App\Services\MySqlToSqliteCacheService;
use App\Models\User;
use App\Models\Ad;
use App\Models\PriceAlert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PriceDropNotification;

/**
 * Service for price drop alerts for saved items
 */
class PriceDropAlertService
{
    protected $cacheService;
    
    public function __construct(MySqlToSqliteCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Create a price drop alert for a user
     */
    public function createPriceAlert(int $userId, int $adId, float $targetPrice): array
    {
        $user = User::find($userId);
        $ad = Ad::find($adId);
        
        if (!$user || !$ad) {
            return ['success' => false, 'message' => 'User or Ad not found'];
        }
        
        // Check if alert already exists
        $existingAlert = PriceAlert::where('user_id', $userId)
            ->where('ad_id', $adId)
            ->first();
        
        if ($existingAlert) {
            return [
                'success' => false,
                'message' => 'Alert already exists for this item'
            ];
        }
        
        // Create the price alert
        $alert = new PriceAlert();
        $alert->user_id = $userId;
        $alert->ad_id = $adId;
        $alert->target_price = $targetPrice;
        $alert->current_price = $ad->price;
        $alert->save();
        
        return [
            'success' => true,
            'message' => 'Price drop alert created successfully',
            'alert' => $alert
        ];
    }
    
    /**
     * Check all active alerts and trigger notifications if prices dropped
     */
    public function checkPriceDrops(): array
    {
        $alerts = PriceAlert::where('active', true)->get();
        $triggeredAlerts = [];
        
        foreach ($alerts as $alert) {
            $ad = Ad::find($alert->ad_id);
            if (!$ad) {
                // Remove alert if ad no longer exists
                $alert->active = false;
                $alert->save();
                continue;
            }
            
            // Check if price dropped below target price
            if ($ad->price <= $alert->target_price && $ad->price < $alert->current_price) {
                // Send notification to user
                $user = User::find($alert->user_id);
                if ($user) {
                    $user->notify(new PriceDropNotification($ad, $alert));
                    $triggeredAlerts[] = [
                        'alert_id' => $alert->id,
                        'user_id' => $alert->user_id,
                        'ad_id' => $alert->ad_id,
                        'old_price' => $alert->current_price,
                        'new_price' => $ad->price,
                        'target_price' => $alert->target_price
                    ];
                }
                
                // Update alert status
                $alert->current_price = $ad->price;
                $alert->last_triggered = now();
                
                // Optionally disable alert after triggering to prevent spam
                // $alert->active = false; 
                
                $alert->save();
            }
        }
        
        return [
            'alerts_checked' => count($alerts),
            'alerts_triggered' => count($triggeredAlerts),
            'triggered_alerts' => $triggeredAlerts
        ];
    }
    
    /**
     * Get active price alerts for a user
     */
    public function getUserPriceAlerts(int $userId, array $filters = []): array
    {
        $cacheKey = "user_price_alerts_{$userId}_" . md5(serialize($filters));
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($userId, $filters) {
                return $this->getActiveAlerts($userId, $filters);
            },
            300 // Cache for 5 minutes
        );
    }
    
    /**
     * Get active alerts for a user
     */
    private function getActiveAlerts(int $userId, array $filters): array
    {
        $query = PriceAlert::with(['ad', 'ad.user', 'ad.category', 'ad.images'])
            ->where('user_id', $userId);
        
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }
        
        if (isset($filters['category_id'])) {
            $query->whereHas('ad', function($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }
        
        if (isset($filters['price_range'])) {
            if (isset($filters['price_range']['min'])) {
                $query->where('target_price', '>=', $filters['price_range']['min']);
            }
            if (isset($filters['price_range']['max'])) {
                $query->where('target_price', '<=', $filters['price_range']['max']);
            }
        }
        
        $alerts = $query->orderBy('created_at', 'desc')->get();
        
        return [
            'alerts' => $alerts,
            'total_count' => $alerts->count(),
            'filters_applied' => $filters
        ];
    }
    
    /**
     * Update a price alert
     */
    public function updatePriceAlert(int $alertId, array $updates): array
    {
        $alert = PriceAlert::find($alertId);
        if (!$alert) {
            return ['success' => false, 'message' => 'Alert not found'];
        }
        
        foreach ($updates as $key => $value) {
            if (in_array($key, ['target_price', 'active'])) {
                $alert->$key = $value;
            }
        }
        
        $alert->save();
        
        return [
            'success' => true,
            'message' => 'Price alert updated successfully',
            'alert' => $alert
        ];
    }
    
    /**
     * Delete a price alert
     */
    public function deletePriceAlert(int $alertId, int $userId): array
    {
        $alert = PriceAlert::where('id', $alertId)
            ->where('user_id', $userId)
            ->first();
        
        if (!$alert) {
            return ['success' => false, 'message' => 'Alert not found or access denied'];
        }
        
        $alert->delete();
        
        return [
            'success' => true,
            'message' => 'Price alert deleted successfully'
        ];
    }
}