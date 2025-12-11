<?php

namespace App\Services\LandingPage;

use App\Models\Configuration;
use App\Models\Ad;
use App\Models\ECommerce\Category;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DynamicLandingService
{
    private $cacheTimeout = 3600; // 1 hour cache timeout

    /**
     * Get dynamic content for the landing page
     *
     * @return array
     */
    public function getLandingPageContent($userId = null)
    {
        $cacheKey = "landing_page_content_" . ($userId ?? 'guest');

        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($userId) {
            return [
                'hero_banner' => $this->getHeroBanner(),
                'popular_categories' => $this->getPopularCategories(),
                'featured_ads' => $this->getFeaturedAds(),
                'latest_ads' => $this->getLatestAds(),
                'trending_searches' => $this->getTrendingSearches(),
                'how_it_works_steps' => $this->getHowItWorksSteps(),
                'app_features' => $this->getAppFeatures(),
            ];
        });
    }

    /**
     * Get hero banner configuration
     *
     * @return array
     */
    public function getHeroBanner()
    {
        $heroConfig = Configuration::getValue('hero_banner', [
            'enabled' => true,
            'type' => 'slider', // 'slider', 'single_image', 'video'
            'slides' => [
                [
                    'title' => 'VidiaSpot Marketplace',
                    'subtitle' => 'Buy and Sell Near You',
                    'description' => 'Find great deals and sell items to people in your community',
                    'cta_text' => 'Shop Now',
                    'cta_url' => '/ads',
                    'image_url' => 'https://images.unsplash.com/photo-1504805572947-34fad45aed93?auto=format&fit=crop&w=2070&q=80',
                    'background_color' => '#388e3c',
                    'text_color' => '#ffffff',
                ]
            ]
        ]);

        return $heroConfig;
    }

    /**
     * Get popular categories to display on landing page
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularCategories()
    {
        $categoryLimit = Configuration::getValue('popular_categories_limit', 6);
        $showFeaturedOnly = Configuration::getValue('show_featured_categories_only', false);

        $query = Category::withCount(['ads' => function($query) {
            $query->where('status', 'active');
        }])
        ->where('is_active', true)
        ->orderBy('ads_count', 'desc')
        ->orderBy('order', 'asc');

        if ($showFeaturedOnly) {
            $query->where('is_featured', true);
        }

        return $query->limit($categoryLimit)->get();
    }

    /**
     * Get featured ads to display on landing page
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedAds()
    {
        $featuredLimit = Configuration::getValue('featured_ads_limit', 4);
        $featuredAds = Ad::with(['user', 'category', 'images'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit($featuredLimit)
            ->get();

        return $featuredAds;
    }

    /**
     * Get latest ads to display on landing page
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatestAds()
    {
        $latestLimit = Configuration::getValue('latest_ads_limit', 8);
        $includeFarmProducts = Configuration::getValue('include_farm_products_in_latest', true);
        $includeFoodServices = Configuration::getValue('include_food_services_in_latest', true);
        $includeGeneralAds = Configuration::getValue('include_general_ads_in_latest', true);

        $query = Ad::with(['user', 'category', 'images'])
            ->where('status', 'active');

        if (!$includeFarmProducts) {
            $query->where('direct_from_farm', false);
        }

        if (!$includeFoodServices) {
            $query->whereDoesntHave('category', function($q) {
                $q->whereIn('slug', ['food-services', 'restaurants', 'food-vendors']);
            });
        }

        if (!$includeGeneralAds) {
            $query->where('direct_from_farm', true);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->limit($latestLimit)
            ->get();
    }

    /**
     * Get trending searches for the landing page
     *
     * @return array
     */
    public function getTrendingSearches()
    {
        // This would typically come from a trending search service
        // For now, we'll return empty as it's already handled in the controller
        
        return [
            'mobile_phones' => [],
            'laptops' => [],
            'vehicles' => [],
        ]; 
    }

    /**
     * Get how it works steps
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHowItWorksSteps()
    {
        // This would typically be retrieved from the HowItWorksStep model
        $steps = \App\Models\HowItWorksStep::active()->ordered()->get();
        return $steps;
    }

    /**
     * Get app features configuration for the features section
     *
     * @return array
     */
    public function getAppFeatures()
    {
        $enabledFeatures = Configuration::getValue('app_features_enabled', [
            'classified_ads' => true,
            'farm_products' => true,
            'food_vending' => true,
            'shop_maker' => true,
            'logistics' => true,
            'pay_later' => true,
            'crypto_p2p' => true,
            'nft_marketplace' => false, // Disabled by default
            'metaverse_shops' => false, // Disabled by default
            'iot_integration' => false, // Disabled by default
        ]);

        $featureCountries = Configuration::getValue('app_features_countries', [
            'classified_ads' => ['all'], // Available everywhere
            'farm_products' => ['all'],
            'food_vending' => ['all'],
            'shop_maker' => ['all'],
            'logistics' => ['all'],
            'pay_later' => ['all'],
            'crypto_p2p' => ['NG', 'KE', 'UG', 'GH', 'ZA'], // Available only in select countries
            'nft_marketplace' => ['US', 'UK', 'CA', 'NG'], // Available in specific countries
            'metaverse_shops' => ['US', 'UK', 'CA'], // Available in specific countries
            'iot_integration' => ['US', 'UK', 'CA', 'NG'], // Available in specific countries
        ]);

        // Get current user's country or default to Nigeria
        $userCountry = request()->ip() ? $this->getUserCountry() : 'NG';

        $features = [];
        foreach ($enabledFeatures as $feature => $isEnabled) {
            if ($isEnabled) {
                // Check if feature is available in user's country
                $countries = $featureCountries[$feature] ?? ['all'];
                if (in_array('all', $countries) || in_array($userCountry, $countries)) {
                    $features[] = $this->getFeatureDetails($feature);
                }
            }
        }

        return $features;
    }

    /**
     * Get details for a specific feature
     *
     * @param string $feature
     * @return array
     */
    private function getFeatureDetails($feature)
    {
        $featureIcons = [
            'classified_ads' => 'fas fa-store',
            'farm_products' => 'fas fa-leaf',
            'food_vending' => 'fas fa-utensils',
            'shop_maker' => 'fas fa-shopping-bag',
            'logistics' => 'fas fa-truck',
            'pay_later' => 'fas fa-calendar-check',
            'crypto_p2p' => 'fas fa-bitcoin',
            'nft_marketplace' => 'fas fa-cube',
            'metaverse_shops' => 'fas fa-globe',
            'iot_integration' => 'fas fa-microchip',
        ];

        $featureTitles = [
            'classified_ads' => 'Classified Ads',
            'farm_products' => 'Farm Products',
            'food_vending' => 'Food Vending',
            'shop_maker' => 'Shop Maker',
            'logistics' => 'Logistics',
            'pay_later' => 'Pay Later',
            'crypto_p2p' => 'Crypto P2P',
            'nft_marketplace' => 'NFT Marketplace',
            'metaverse_shops' => 'Metaverse Shops',
            'iot_integration' => 'IoT Integration',
        ];

        $featureDescriptions = [
            'classified_ads' => 'Buy and sell general items in your community',
            'farm_products' => 'Get fresh products directly from local farms',
            'food_vending' => 'Order food from nearby restaurants and vendors',
            'shop_maker' => 'Create your own online store',
            'logistics' => 'Fast and reliable delivery services',
            'pay_later' => 'Buy now, pay later options available',
            'crypto_p2p' => 'Peer-to-peer cryptocurrency trading',
            'nft_marketplace' => 'Buy and sell non-fungible tokens',
            'metaverse_shops' => 'Virtual shopping experiences in metaverse',
            'iot_integration' => 'Smart device integration in shopping',
        ];

        $featureRoutes = [
            'classified_ads' => '/ads',
            'farm_products' => '/farm-marketplace',
            'food_vending' => '/food-vending',
            'shop_maker' => '/shop-maker',
            'logistics' => '/logistics',
            'pay_later' => '/pay-later',
            'crypto_p2p' => '/crypto-p2p',
            'nft_marketplace' => '/nft-marketplace',
            'metaverse_shops' => '/metaverse',
            'iot_integration' => '/iot',
        ];

        return [
            'feature' => $feature,
            'title' => $featureTitles[$feature] ?? $feature,
            'description' => $featureDescriptions[$feature] ?? 'Feature description',
            'icon' => $featureIcons[$feature] ?? 'fas fa-cogs',
            'route' => $featureRoutes[$feature] ?? '/',
            'is_enabled' => true,
        ];
    }

    /**
     * Get user's country based on IP
     *
     * @return string
     */
    private function getUserCountry()
    {
        // For now, return Nigeria as default
        // In a real implementation, you would use a geolocation service
        return 'NG';
    }

    /**
     * Update hero banner configuration
     *
     * @param array $data
     * @return bool
     */
    public function updateHeroBanner(array $data)
    {
        Configuration::setValue('hero_banner', $data, 'json', 'landing_page', 'Hero banner configuration');
        Cache::forget('landing_page_content_guest');
        
        return true;
    }

    /**
     * Update app features configuration
     *
     * @param array $features
     * @return bool
     */
    public function updateAppFeatures(array $features, array $countries = null)
    {
        Configuration::setValue('app_features_enabled', $features['enabled'] ?? [], 'json', 'features', 'App features enabled configuration');
        
        if ($countries) {
            Configuration::setValue('app_features_countries', $countries, 'json', 'features', 'App features country restrictions configuration');
        }
        
        Cache::flush(); // Clear all cached content
        
        return true;
    }
}