<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\ECommerce\Category;
use App\Services\TrendingSearchService;
use App\Services\RecommendationService;
use App\Services\NotificationPreferenceService;
use App\Services\IoTDeviceService;
use App\Services\NftService;
use App\Services\MetaverseService;
use App\Services\DroneDeliveryService;
use App\Services\AICustomerServiceAvatar;
use App\Services\PredictiveMaintenanceService;
use App\Services\SmartContractService;
use App\Services\CryptoP2PService;
use App\Services\LandingPage\DynamicLandingService;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    protected $trendingSearchService;
    protected $recommendationService;
    protected $notificationService;
    protected $iotDeviceService;
    protected $nftService;
    protected $metaverseService;
    protected $droneDeliveryService;
    protected $aiCustomerServiceAvatar;
    protected $predictiveMaintenanceService;
    protected $smartContractService;
    protected $cryptoP2PService;
    protected $dynamicLandingService;

    public function __construct(
        TrendingSearchService $trendingSearchService,
        RecommendationService $recommendationService,
        NotificationPreferenceService $notificationService,
        IoTDeviceService $iotDeviceService,
        NftService $nftService,
        MetaverseService $metaverseService,
        DroneDeliveryService $droneDeliveryService,
        AICustomerServiceAvatar $aiCustomerServiceAvatar,
        PredictiveMaintenanceService $predictiveMaintenanceService,
        SmartContractService $smartContractService,
        CryptoP2PService $cryptoP2PService,
        DynamicLandingService $dynamicLandingService
    ) {
        $this->trendingSearchService = $trendingSearchService;
        $this->recommendationService = $recommendationService;
        $this->notificationService = $notificationService;
        $this->iotDeviceService = $iotDeviceService;
        $this->nftService = $nftService;
        $this->metaverseService = $metaverseService;
        $this->droneDeliveryService = $droneDeliveryService;
        $this->aiCustomerServiceAvatar = $aiCustomerServiceAvatar;
        $this->predictiveMaintenanceService = $predictiveMaintenanceService;
        $this->smartContractService = $smartContractService;
        $this->cryptoP2PService = $cryptoP2PService;
        $this->dynamicLandingService = $dynamicLandingService;
    }

    public function index()
    {
        $user = Auth::user();
        $moodState = null;

        // Get personalized content for logged-in users
        if ($user) {
            $moodState = $this->notificationService->getMoodState($user);

            // Get personalized recommendations if user is logged in
            $personalizedAds = $this->recommendationService->getMoodBasedRecommendations($user, $moodState, 8);
        } else {
            $personalizedAds = collect();
        }

        // Get dynamic landing page content using the service
        $landingContent = $this->dynamicLandingService->getLandingPageContent($user?->id);

        // Get featured ads (limited for performance)
        $featuredAds = $landingContent['featured_ads'];

        // Get latest ads
        $latestAds = $landingContent['latest_ads'];

        // Get popular categories
        $popularCategories = $landingContent['popular_categories'];

        // Get trending searches from database
        $trendingSearches = $this->trendingSearchService->getTrendingSearches(12, 7);

        // Group trending searches by categories for display
        $trendingByCategory = [
            'mobile_phones' => collect($trendingSearches)->filter(function($search) {
                return stripos($search->query, 'iphone') !== false ||
                       stripos($search->query, 'samsung') !== false ||
                       stripos($search->query, 'android') !== false ||
                       stripos($search->query, 'phone') !== false ||
                       stripos($search->query, 'mobile') !== false;
            })->take(3),
            'laptops' => collect($trendingSearches)->filter(function($search) {
                return stripos($search->query, 'laptop') !== false ||
                       stripos($search->query, 'macbook') !== false ||
                       stripos($search->query, 'computer') !== false ||
                       stripos($search->query, 'desktop') !== false;
            })->take(3),
            'vehicles' => collect($trendingSearches)->filter(function($search) {
                return stripos($search->query, 'toyota') !== false ||
                       stripos($search->query, 'honda') !== false ||
                       stripos($search->query, 'car') !== false ||
                       stripos($search->query, 'vehicle') !== false ||
                       stripos($search->query, 'nissan') !== false ||
                       stripos($search->query, 'hundai') !== false;
            })->take(3),
        ];

        // Add fallback trending searches if no data from database
        if ($trendingByCategory['mobile_phones']->isEmpty()) {
            $trendingByCategory['mobile_phones'] = collect([
                (object)['query' => 'iPhone', 'count' => 10],
                (object)['query' => 'Samsung', 'count' => 8],
                (object)['query' => 'Android', 'count' => 6],
            ]);
        }

        if ($trendingByCategory['laptops']->isEmpty()) {
            $trendingByCategory['laptops'] = collect([
                (object)['query' => 'Laptop', 'count' => 12],
                (object)['query' => 'Desktop PC', 'count' => 7],
                (object)['query' => 'MacBook', 'count' => 5],
            ]);
        }

        if ($trendingByCategory['vehicles']->isEmpty()) {
            $trendingByCategory['vehicles'] = collect([
                (object)['query' => 'Toyota', 'count' => 15],
                (object)['query' => 'Honda', 'count' => 9],
                (object)['query' => 'Car', 'count' => 8],
            ]);
        }

        // Get how it works steps for display
        $howItWorksSteps = $this->getHowItWorksSteps();

        // Get app features for dynamic display in features section
        $appFeatures = $landingContent['app_features'];

        // Get innovative features data
        $innovativeFeatures = [
            'iot_smart_home' => [
                'count' => $user ? $this->iotDeviceService->getSmartHomeDevices()->count() : 0,
                'active_count' => $user ? $this->iotDeviceService->getConnectedDevices()->count() : 0,
            ],
            'nft_marketplace' => [
                'collections_count' => $this->nftService->getMarketplaceNfts(['per_page' => 1])->total(),
                'featured_nfts' => $this->nftService->getMarketplaceNfts(['per_page' => 3, 'is_listed' => true]),
            ],
            'metaverse_showrooms' => [
                'active_showrooms' => $this->metaverseService->getTrendingShowrooms(3),
                'featured_showrooms' => $this->metaverseService->getFeaturedShowrooms(3),
            ],
            'drone_delivery' => [
                'active_missions' => $user ? $this->droneDeliveryService->getActiveMissions()->count() : 0,
                'available_drones' => $this->droneDeliveryService->getAvailableDrones()->count(),
            ],
            'ai_customer_service' => [
                'capabilities' => $this->aiCustomerServiceAvatar->getAvatarCapabilities(),
                'personality' => $this->aiCustomerServiceAvatar->getAvatarPersonality(),
            ],
            'predictive_maintenance' => [
                'urgent_maintenance' => $user ? $this->predictiveMaintenanceService->getUrgentMaintenanceNeeds()->count() : 0,
                'predicted_maintenance' => $user ? $this->predictiveMaintenanceService->getMaintenanceAlerts()->count() : 0,
            ],
            'smart_contracts' => [
                'active_contracts' => $this->smartContractService->getActiveContracts()->count(),
                'recent_transactions' => $user ? $this->smartContractService->getRecentTransactions(null, 3) : collect(),
            ],
            'crypto_p2p_marketplace' => [
                'total_listings' => $this->cryptoP2PService->getActiveListings(['per_page' => 1])->total(),
                'active_trades' => $user ? $this->cryptoP2PService->getUserTrades($user->id)->count() : 0,
                'featured_listings' => $this->cryptoP2PService->getActiveListings(['per_page' => 3]),
                'trading_volume' => $user ? $this->cryptoP2PService->getTradeStatistics($user->id)['total_volume'] ?? 0 : 0,
            ],
        ];

        // Get how it works steps for display
        $howItWorksSteps = $this->getHowItWorksSteps();

        // Get app features for dynamic display in features section
        $appFeatures = $landingContent['app_features'];

        return view('landing.index', compact(
            'featuredAds',
            'latestAds',
            'popularCategories',
            'trendingByCategory',
            'howItWorksSteps',
            'personalizedAds',
            'moodState',
            'innovativeFeatures',
            'appFeatures'  // Pass app features for dynamic display
        ));
    }

    private function getHowItWorksSteps()
    {
        return \App\Models\HowItWorksStep::active()->ordered()->get();
    }

    public function innovativeFeatures()
    {
        $user = Auth::user();

        // Get comprehensive data for all innovative features
        $innovativeFeaturesData = [
            'iot_smart_home' => [
                'count' => $user ? $this->iotDeviceService->getSmartHomeDevices($user->id)->count() : 0,
                'active_count' => $user ? $this->iotDeviceService->getConnectedDevices($user->id)->count() : 0,
                'devices' => $user ? $this->iotDeviceService->getSmartHomeDevices($user->id) : collect(),
            ],
            'nft_marketplace' => [
                'collections_count' => $this->nftService->getMarketplaceNfts(['per_page' => 1])->total(),
                'featured_nfts' => $this->nftService->getMarketplaceNfts(['per_page' => 6, 'is_listed' => true]),
                'user_collections' => $user ? $this->nftService->getUserCollections($user->id) : collect(),
                'user_nfts' => $user ? $this->nftService->getUserNfts($user->id) : collect(),
            ],
            'metaverse_showrooms' => [
                'active_showrooms' => $this->metaverseService->getTrendingShowrooms(6),
                'featured_showrooms' => $this->metaverseService->getFeaturedShowrooms(6),
                'user_showrooms' => $user ? $this->metaverseService->getActiveShowrooms(['owner_id' => $user->id]) : collect(),
            ],
            'drone_delivery' => [
                'active_missions' => $user ? $this->droneDeliveryService->getActiveMissions()->count() : 0,
                'available_drones' => $this->droneDeliveryService->getAvailableDrones()->count(),
                'active_missions_list' => $user ? $this->droneDeliveryService->getActiveMissions() : collect(),
            ],
            'ai_customer_service' => [
                'capabilities' => $this->aiCustomerServiceAvatar->getAvatarCapabilities(),
                'personality' => $this->aiCustomerServiceAvatar->getAvatarPersonality(),
                'session_token' => $user ? $this->aiCustomerServiceAvatar->createAvatarSession($user->id)->session_token ?? null : null,
            ],
            'predictive_maintenance' => [
                'urgent_maintenance' => $user ? $this->predictiveMaintenanceService->getUrgentMaintenanceNeeds($user->id)->count() : 0,
                'predicted_maintenance' => $user ? $this->predictiveMaintenanceService->getMaintenanceAlerts($user->id)->count() : 0,
                'maintenance_recommendations' => $user ? $this->predictiveMaintenanceService->getMaintenanceRecommendations($user->id) : collect(),
                'maintenance_insights' => $user ? $this->predictiveMaintenanceService->getPredictiveInsights($user->id) : [],
            ],
            'smart_contracts' => [
                'active_contracts' => $this->smartContractService->getActiveContracts()->count(),
                'recent_transactions' => $user ? $this->smartContractService->getRecentTransactions($user->id, 6) : collect(),
                'user_contracts' => $user ? $this->smartContractService->getActiveContracts($user->id) : collect(),
            ],
            'crypto_p2p_marketplace' => [
                'total_listings' => $this->cryptoP2PService->getActiveListings(['per_page' => 1])->total(),
                'active_trades' => $user ? $this->cryptoP2PService->getUserTrades($user->id)->count() : 0,
                'featured_listings' => $this->cryptoP2PService->getActiveListings(['per_page' => 6]),
                'trading_volume' => $user ? $this->cryptoP2PService->getTradeStatistics($user->id)['total_volume'] ?? 0 : 0,
                'user_listings' => $user ? $this->cryptoP2PService->getUserListings($user->id) : collect(),
            ],
        ];

        return view('landing.innovative-features', compact('innovativeFeaturesData'));
    }
}



    