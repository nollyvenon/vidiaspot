// Platform Configuration File
// Sets up the application to behave like specialized platforms

const platformConfig = {
  // Chowdeck-like Food Ordering System Configuration
  foodOrderingConfig: {
    // Enables restaurant-style food ordering similar to Chowdeck
    enabled: true,
    // Allows multiple vendors/restaurants
    multiVendorSupport: true,
    // Enables real-time order tracking
    orderTracking: true,
    // Enables delivery management
    deliveryManagement: true,
    // Enables menu customization
    menuCustomization: true,
    // Allows special instructions per order
    specialInstructions: true,
    // Enables rating and review system
    ratingSystem: true,
    // Enables loyalty/points system
    loyaltyProgram: true,
    // Enables food categories and filtering
    categoryFiltering: true,
    // Enables dietary restrictions filtering
    dietaryFiltering: true,
    // Enables order scheduling
    orderScheduling: true
  },

  // Shopify-like E-commerce System Configuration
  ecommerceConfig: {
    // Enables SaaS-style e-commerce similar to Shopify
    enabled: true,
    // Enables multi-tenant store management
    multiTenant: true,
    // Enables custom domain linking
    customDomains: true,
    // Enables theme/personalization system
    themes: true,
    // Enables payment gateway integrations
    paymentGateways: ['stripe', 'paypal', 'paystack', 'flutterwave'],
    // Enables inventory management
    inventoryManagement: true,
    // Enables order management
    orderManagement: true,
    // Enables customer management
    customerManagement: true,
    // Enables analytics
    analytics: true,
    // Enables discount codes
    discountCodes: true,
    // Enables shipping management
    shipping: true,
    // Enables tax calculation
    taxCalculation: true,
    // Enables multi-channel selling
    multiChannel: true,
    // Enables subscription services
    subscriptions: true,
    // Enables abandoned cart recovery
    abandonedCartRecovery: true
  },

  // Affiliate/MLM Configuration
  affiliateConfig: {
    // Enables affiliate program
    enabled: true,
    // Enables commission tracking
    commissionTracking: true,
    // Enables referral code generation
    referralCodes: true,
    // Enables payout management
    payouts: true,
    // Enables multi-level marketing (MLM)
    mlmEnabled: true,
    // Sets MLM commission structure depth
    mlmDepth: 5,
    // Enables downline tracking
    downlineTracking: true,
    // Enables commission on direct referrals
    directCommission: true,
    // Enables commission on upline referrals
    uplineCommission: true
  },

  // Import/Export Configuration
  importExportConfig: {
    // Enables platform import functionality
    importEnabled: true,
    // Supported platforms for import
    supportedPlatforms: ['shopify', 'woocommerce', 'bigcommerce', 'magento', 'custom_csv'],
    // Enables export functionality
    exportEnabled: true,
    // Data types that can be imported/exported
    supportedDataTypes: ['products', 'customers', 'orders', 'categories', 'inventory'],
    // Enables bulk operations
    bulkOperations: true
  },

  // Security & Compliance Configuration
  securityConfig: {
    // Enables SSL certificate by default
    forceSSL: true,
    // Enables payment card industry compliance
    pciCompliance: true,
    // Enables data privacy (GDPR/CCPA) compliance
    privacyCompliance: true,
    // Enables fraud protection
    fraudProtection: true,
    // Enables two-factor authentication
    twoFactorAuth: true,
    // Enables role-based permissions
    rbacEnabled: true
  },

  // Performance & Scaling Configuration
  performanceConfig: {
    // Enables caching
    cachingEnabled: true,
    // Enables CDN integration
    cdnEnabled: true,
    // Enables database optimization
    dbOptimization: true,
    // Enables image optimization
    imageOptimization: true,
    // Enables compression
    compression: true,
    // Enables load balancing
    loadBalancing: true
  },

  // API Configuration
  apiConfig: {
    // Enables REST APIs
    restApi: true,
    // Enables GraphQL API
    graphqlApi: true,
    // Enables webhook support
    webhooks: true,
    // Sets API versioning
    versioning: 'v1',
    // Enables API rate limiting
    rateLimiting: true,
    // Enables API documentation
    apiDocumentation: true
  },

  // Feature Flags for Platform Customization
  featureFlags: {
    // Enable food ordering system
    foodOrdering: true,
    // Enable e-commerce system
    ecommerce: true,
    // Enable marketplace features
    marketplace: true,
    // Enable subscription billing
    subscriptions: true,
    // Enable multi-vendor features
    multiVendor: true,
    // Enable social features
    socialFeatures: true,
    // Enable AI-powered features
    aiFeatures: true,
    // Enable advanced reporting
    advancedReporting: true,
    // Enable mobile app support
    mobileApp: true
  }
};

export default platformConfig;