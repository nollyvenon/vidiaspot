<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\VoiceSearchService;
use App\Services\AI\VisualSearchService;
use App\Services\AI\ARViewService;
use App\Services\AI\SocialSearchService;
use App\Services\AI\TrendingRecommendationsService;
use App\Services\AI\PriceDropAlertService;
use App\Services\AI\GeographicHeatMapService;
use App\Services\MySqlToSqliteCacheService;

class AdvancedSearchDiscoveryTest extends TestCase
{
    public function test_voice_search_service_can_be_resolved()
    {
        $service = $this->app->make(VoiceSearchService::class);

        $this->assertInstanceOf(VoiceSearchService::class, $service);
    }

    public function test_visual_search_service_can_be_resolved()
    {
        $service = $this->app->make(VisualSearchService::class);

        $this->assertInstanceOf(VisualSearchService::class, $service);
    }

    public function test_ar_view_service_can_be_resolved()
    {
        $service = $this->app->make(ARViewService::class);

        $this->assertInstanceOf(ARViewService::class, $service);
    }

    public function test_social_search_service_can_be_resolved()
    {
        $service = $this->app->make(SocialSearchService::class);

        $this->assertInstanceOf(SocialSearchService::class, $service);
    }

    public function test_trending_recommendations_service_can_be_resolved()
    {
        $service = $this->app->make(TrendingRecommendationsService::class);

        $this->assertInstanceOf(TrendingRecommendationsService::class, $service);
    }

    public function test_price_drop_alert_service_can_be_resolved()
    {
        $service = $this->app->make(PriceDropAlertService::class);

        $this->assertInstanceOf(PriceDropAlertService::class, $service);
    }

    public function test_geographic_heat_map_service_can_be_resolved()
    {
        $service = $this->app->make(GeographicHeatMapService::class);

        $this->assertInstanceOf(GeographicHeatMapService::class, $service);
    }
    
    public function test_services_have_expected_methods()
    {
        $voiceService = $this->app->make(VoiceSearchService::class);
        $visualService = $this->app->make(VisualSearchService::class);
        $arService = $this->app->make(ARViewService::class);
        $socialService = $this->app->make(SocialSearchService::class);
        $trendingService = $this->app->make(TrendingRecommendationsService::class);
        $priceAlertService = $this->app->make(PriceDropAlertService::class);
        $geoService = $this->app->make(GeographicHeatMapService::class);
        
        // Check Voice Search Service methods
        $this->assertTrue(method_exists($voiceService, 'processVoiceSearch'));
        $this->assertTrue(method_exists($voiceService, 'processNaturalLanguageQuery'));
        
        // Check Visual Search Service methods
        $this->assertTrue(method_exists($visualService, 'performVisualSearch'));
        $this->assertTrue(method_exists($visualService, 'performReverseImageSearch'));
        
        // Check AR View Service methods
        $this->assertTrue(method_exists($arService, 'getARViewData'));
        $this->assertTrue(method_exists($arService, 'getARSessionData'));
        
        // Check Social Search Service methods
        $this->assertTrue(method_exists($socialService, 'findListingsFromFriendsNetwork'));
        $this->assertTrue(method_exists($socialService, 'getFriendRecommendations'));
        
        // Check Trending Recommendations Service methods
        $this->assertTrue(method_exists($trendingService, 'getTrendingItems'));
        $this->assertTrue(method_exists($trendingService, 'getSeasonalRecommendations'));
        
        // Check Price Drop Alert Service methods
        $this->assertTrue(method_exists($priceAlertService, 'createPriceAlert'));
        $this->assertTrue(method_exists($priceAlertService, 'checkPriceDrops'));
        
        // Check Geographic Heat Map Service methods
        $this->assertTrue(method_exists($geoService, 'generateHeatMap'));
        $this->assertTrue(method_exists($geoService, 'getTrendingLocationsForCategory'));
    }
}