<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use App\Services\MySqlToSqliteCacheService;
use App\Models\Ad;
use App\Models\City;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * Service for geographic heat maps showing high-demand areas
 */
class GeographicHeatMapService
{
    protected $cacheService;
    
    public function __construct(MySqlToSqliteCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Generate geographic heat map data for high-demand areas
     */
    public function generateHeatMap(array $options = []): array
    {
        $cacheKey = "geographic_heatmap_" . md5(serialize($options));
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($options) {
                return $this->computeHeatMapData($options);
            },
            3600 // Cache for 1 hour
        );
    }
    
    /**
     * Compute heat map data based on demand patterns
     */
    private function computeHeatMapData(array $options): array
    {
        $categoryId = $options['category_id'] ?? null;
        $timeFrame = $options['time_frame'] ?? 'month'; // day, week, month
        $location = $options['location'] ?? null; // country, state, or city
        $minDemand = $options['min_demand'] ?? 1;
        
        // Get date range
        $dateFrom = $this->getDateFromTimeFrame($timeFrame);
        
        // Build query
        $query = Ad::select([
            'location',
            'city_id',
            'state',
            'country',
            DB::raw('COUNT(*) as demand_count'),
            DB::raw('AVG(price) as average_price'),
            DB::raw('MIN(price) as min_price'),
            DB::raw('MAX(price) as max_price'),
            DB::raw('SUM(view_count) as total_views')
        ])
        ->where('status', 'active')
        ->where('created_at', '>', $dateFrom)
        ->groupBy('location', 'city_id', 'state', 'country');
        
        // Apply filters
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        if ($location) {
            // This would depend on how location is stored in the database
            $query->where(function($q) use ($location) {
                $q->where('location', 'LIKE', "%{$location}%")
                  ->orWhere('city', 'LIKE', "%{$location}%")
                  ->orWhere('state', 'LIKE', "%{$location}%")
                  ->orWhere('country', 'LIKE', "%{$location}%");
            });
        }
        
        $demandData = $query->having('demand_count', '>=', $minDemand)
            ->orderByDesc('demand_count')
            ->get();
        
        // Process and format the data for heat map visualization
        $heatMapData = $this->formatHeatMapData($demandData);
        
        return [
            'heat_map_data' => $heatMapData,
            'metadata' => [
                'total_locations' => count($heatMapData),
                'time_frame' => $timeFrame,
                'date_from' => $dateFrom->toISOString(),
                'date_to' => now()->toISOString(),
                'category_filter' => $categoryId,
                'location_filter' => $location,
                'demand_threshold' => $minDemand,
                'data_computed_at' => now()->toISOString()
            ],
            'summary_stats' => [
                'highest_demand_location' => $this->getHighestDemandLocation($heatMapData),
                'average_demand' => $this->getAverageDemand($heatMapData),
                'top_categories_by_location' => $this->getTopCategoriesByLocation($options)
            ]
        ];
    }
    
    /**
     * Format heat map data for visualization
     */
    private function formatHeatMapData($demandData): array
    {
        $formattedData = [];
        $maxDemand = $demandData->max('demand_count') ?: 1;
        
        foreach ($demandData as $datum) {
            $formattedData[] = [
                'location' => $datum->location,
                'city_id' => $datum->city_id,
                'state' => $datum->state,
                'country' => $datum->country,
                'demand_count' => $datum->demand_count,
                'average_price' => $datum->average_price,
                'min_price' => $datum->min_price,
                'max_price' => $datum->max_price,
                'total_views' => $datum->total_views,
                'demand_intensity' => $this->calculateDemandIntensity($datum->demand_count, $maxDemand),
                'demand_level' => $this->categorizeDemandLevel($datum->demand_count),
                'coordinates' => $this->getLocationCoordinates($datum->location),
                'density_score' => $this->calculateDensityScore($datum)
            ];
        }
        
        return $formattedData;
    }
    
    /**
     * Calculate demand intensity (0-1 scale)
     */
    private function calculateDemandIntensity(int $demandCount, int $maxDemand): float
    {
        return min(1.0, $demandCount / $maxDemand);
    }
    
    /**
     * Categorize demand level
     */
    private function categorizeDemandLevel(int $demandCount): string
    {
        if ($demandCount >= 50) return 'very_high';
        if ($demandCount >= 20) return 'high';
        if ($demandCount >= 10) return 'moderate';
        if ($demandCount >= 5) return 'low';
        return 'very_low';
    }
    
    /**
     * Get location coordinates (simulated)
     */
    private function getLocationCoordinates(string $location): ?array
    {
        // In a real implementation, this would call a geocoding API
        // For this demo, we'll return approximate coordinates for major Nigerian cities
        $coordinatesMap = [
            'Lagos' => ['lat' => 6.5244, 'lng' => 3.3792],
            'Abuja' => ['lat' => 9.0765, 'lng' => 7.3986],
            'Port Harcourt' => ['lat' => 4.8026, 'lng' => 7.0221],
            'Kano' => ['lat' => 12.0000, 'lng' => 7.7300],
            'Ibadan' => ['lat' => 7.3775, 'lng' => 3.9470],
            'Benin City' => ['lat' => 6.3381, 'lng' => 5.6253],
            'Kaduna' => ['lat' => 10.5167, 'lng' => 7.4333],
            'Jos' => ['lat' => 9.9325, 'lng' => 8.8661],
            'Enugu' => ['lat' => 6.4469, 'lng' => 7.4874],
            'Awka' => ['lat' => 6.2125, 'lng' => 7.0717],
            'Onitsha' => ['lat' => 6.1587, 'lng' => 6.7808],
            'Aba' => ['lat' => 5.1066, 'lng' => 7.3458],
            'Warri' => ['lat' => 5.5077, 'lng' => 5.7375],
            'Sokoto' => ['lat' => 13.0667, 'lng' => 5.2333],
            'Bauchi' => ['lat' => 10.3158, 'lng' => 9.8443],
            'Maiduguri' => ['lat' => 11.8467, 'lng' => 13.1597],
            'Gombe' => ['lat' => 10.2817, 'lng' => 11.1750],
            'Yola' => ['lat' => 9.2083, 'lng' => 12.4833],
            'Damaturu' => ['lat' => 11.7464, 'lng' => 11.9634],
            'Calabar' => ['lat' => 4.9581, 'lng' => 8.3407]
        ];
        
        foreach ($coordinatesMap as $city => $coords) {
            if (stripos($location, $city) !== false) {
                return $coords;
            }
        }
        
        // If location not found, return Nigeria center
        return ['lat' => 9.0820, 'lng' => 8.6753];
    }
    
    /**
     * Calculate density score for location
     */
    private function calculateDensityScore($datum): float
    {
        // Calculate density score based on multiple factors
        $demandScore = log($datum->demand_count + 1) * 10;
        $priceScore = min(10, $datum->average_price / 100000); // Normalize price score
        $viewScore = log($datum->total_views + 1);
        
        return ($demandScore + $priceScore + $viewScore) / 3;
    }
    
    /**
     * Get highest demand location
     */
    private function getHighestDemandLocation(array $heatMapData): ?array
    {
        if (empty($heatMapData)) {
            return null;
        }
        
        $highest = null;
        foreach ($heatMapData as $data) {
            if (!$highest || $data['demand_count'] > $highest['demand_count']) {
                $highest = $data;
            }
        }
        
        return $highest;
    }
    
    /**
     * Get average demand across all locations
     */
    private function getAverageDemand(array $heatMapData): float
    {
        if (empty($heatMapData)) {
            return 0;
        }
        
        $total = array_sum(array_column($heatMapData, 'demand_count'));
        return $total / count($heatMapData);
    }
    
    /**
     * Get top categories by location
     */
    private function getTopCategoriesByLocation(array $options): array
    {
        $timeFrame = $options['time_frame'] ?? 'month';
        $dateFrom = $this->getDateFromTimeFrame($timeFrame);
        
        // Get top categories for each location
        $categoryQuery = DB::table('ads')
            ->join('categories', 'ads.category_id', '=', 'categories.id')
            ->select([
                'location',
                'categories.name as category_name',
                DB::raw('COUNT(*) as listing_count'),
                DB::raw('AVG(price) as avg_price')
            ])
            ->where('ads.status', 'active')
            ->where('ads.created_at', '>', $dateFrom)
            ->groupBy('location', 'categories.name')
            ->orderBy('listing_count', 'desc');
        
        if (isset($options['category_id'])) {
            $categoryQuery->where('ads.category_id', $options['category_id']);
        }
        
        if (isset($options['location'])) {
            $categoryQuery->where('ads.location', 'LIKE', "%{$options['location']}%");
        }
        
        $categoryData = $categoryQuery->limit(50)->get();
        
        // Group by location
        $byLocation = [];
        foreach ($categoryData as $row) {
            $location = $row->location;
            if (!isset($byLocation[$location])) {
                $byLocation[$location] = [];
            }
            $byLocation[$location][] = [
                'category' => $row->category_name,
                'listing_count' => $row->listing_count,
                'avg_price' => $row->avg_price
            ];
        }
        
        return $byLocation;
    }
    
    /**
     * Get trending locations for a specific category
     */
    public function getTrendingLocationsForCategory(int $categoryId, array $options = []): array
    {
        $timeFrame = $options['time_frame'] ?? 'week';
        $limit = $options['limit'] ?? 10;
        $dateFrom = $this->getDateFromTimeFrame($timeFrame);
        
        $cacheKey = "trending_loc_cat_{$categoryId}_{$timeFrame}_{$limit}";
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($categoryId, $dateFrom, $limit) {
                $trendingLocs = Ad::select([
                    'location',
                    DB::raw('COUNT(*) as new_listings'),
                    DB::raw('SUM(view_count) as total_views'),
                    DB::raw('AVG(price) as avg_price'),
                    DB::raw('MAX(created_at) as latest_listing')
                ])
                ->where('category_id', $categoryId)
                ->where('status', 'active')
                ->where('created_at', '>', $dateFrom)
                ->groupBy('location')
                ->orderByDesc('new_listings')
                ->limit($limit)
                ->get();
                
                $result = [];
                foreach ($trendingLocs as $loc) {
                    $result[] = [
                        'location' => $loc->location,
                        'new_listings' => $loc->new_listings,
                        'total_views' => $loc->total_views,
                        'avg_price' => $loc->avg_price,
                        'latest_listing' => $loc->latest_listing,
                        'growth_rate' => $this->calculateGrowthRate($loc),
                        'coordinates' => $this->getLocationCoordinates($loc->location)
                    ];
                }
                
                return [
                    'trending_locations' => $result,
                    'category_id' => $categoryId,
                    'time_period' => $timeFrame,
                    'computed_at' => now()->toISOString()
                ];
            },
            1800 // Cache for 30 minutes
        );
    }
    
    /**
     * Calculate growth rate for location
     */
    private function calculateGrowthRate($locationData): float
    {
        // This would be calculated based on historical data
        // For this demo, we'll return a simulated growth rate
        return round(rand(5, 50) / 10, 2); // Random growth between 0.5% and 5%
    }
    
    /**
     * Get seasonal location patterns
     */
    public function getSeasonalLocationPatterns(array $options = []): array
    {
        $categoryId = $options['category_id'] ?? null;
        $year = $options['year'] ?? now()->year;
        
        $cacheKey = "seasonal_patterns_{$categoryId}_{$year}";
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($categoryId, $year) {
                // Get monthly data for seasonal patterns
                $data = Ad::select([
                    DB::raw('MONTH(created_at) as month'),
                    'location',
                    DB::raw('COUNT(*) as listing_count')
                ])
                ->whereYear('created_at', $year)
                ->where('status', 'active');
                
                if ($categoryId) {
                    $data->where('category_id', $categoryId);
                }
                
                $data = $data->groupBy('month', 'location')
                    ->orderBy('month')
                    ->get();
                
                // Group by month
                $monthlyPatterns = [];
                foreach ($data as $row) {
                    $month = $row->month;
                    if (!isset($monthlyPatterns[$month])) {
                        $monthlyPatterns[$month] = [];
                    }
                    $monthlyPatterns[$month][] = [
                        'location' => $row->location,
                        'count' => $row->listing_count
                    ];
                }
                
                return [
                    'seasonal_patterns' => $monthlyPatterns,
                    'year' => $year,
                    'category_filter' => $categoryId,
                    'computed_at' => now()->toISOString()
                ];
            },
            86400 // Cache for 24 hours
        );
    }
    
    /**
     * Get demand forecasting for locations
     */
    public function getDemandForecastForLocations(array $options = []): array
    {
        $days = $options['days'] ?? 7;
        $categoryId = $options['category_id'] ?? null;
        
        $cacheKey = "demand_forecast_loc_{$days}_" . md5(serialize($options));
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($days, $categoryId) {
                // In a real implementation, this would use ML to predict demand
                // For this demo, we'll simulate using historical patterns
                
                // Get recent demand patterns
                $recentData = $this->getHeatMapData([
                    'category_id' => $categoryId,
                    'time_frame' => 'month'
                ]);
                
                // Apply growth factors
                $forecast = array_map(function($location) use ($days) {
                    $growthFactor = rand(80, 120) / 100; // Random 80%-120% of current demand
                    return [
                        'location' => $location['location'],
                        'current_demand' => $location['demand_count'],
                        'forecasted_demand' => (int)round($location['demand_count'] * $growthFactor),
                        'growth_percentage' => round(($growthFactor - 1) * 100, 2),
                        'confidence_level' => rand(60, 95) / 100, // 60-95% confidence
                        'coordinates' => $location['coordinates']
                    ];
                }, array_slice($recentData['heat_map_data'], 0, 20)); // Limit to top 20 locations
                
                return [
                    'demand_forecast' => $forecast,
                    'forecast_period_days' => $days,
                    'category_filter' => $categoryId,
                    'computed_at' => now()->toISOString()
                ];
            },
            3600 // Cache for 1 hour
        );
    }
    
    /**
     * Get date from time frame string
     */
    private function getDateFromTimeFrame(string $timeFrame): \Carbon\Carbon
    {
        switch ($timeFrame) {
            case 'day':
                return now()->subDay();
            case 'week':
                return now()->subWeek();
            case 'month':
                return now()->subMonth();
            case 'quarter':
                return now()->subMonths(3);
            case 'year':
                return now()->subYear();
            default:
                return now()->subWeek(); // Default to week
        }
    }
}