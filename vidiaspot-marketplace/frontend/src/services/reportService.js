import apiClient from './api';

const reportService = {
  // Sales & Revenue Reports
  getDailySales: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/daily-sales', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching daily sales:', error);
      throw error;
    }
  },

  getMenuPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/menu-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching menu performance:', error);
      throw error;
    }
  },

  getDeliveryPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching delivery performance:', error);
      throw error;
    }
  },

  getCustomerSpending: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/customer-spending', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching customer spending:', error);
      throw error;
    }
  },

  getPaymentMethodRevenue: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/payment-method', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching payment method revenue:', error);
      throw error;
    }
  },

  // Operational Efficiency Reports
  getKitchenPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/kitchen-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching kitchen performance:', error);
      throw error;
    }
  },

  getDriverPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/driver-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching driver performance:', error);
      throw error;
    }
  },

  getInventoryManagement: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/inventory-management', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching inventory management:', error);
      throw error;
    }
  },

  getLocationAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/location-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching location analytics:', error);
      throw error;
    }
  },

  getEquipmentUtilization: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/equipment-utilization', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching equipment utilization:', error);
      throw error;
    }
  },

  // Order Fulfillment Reports
  getOrderFulfillment: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/order-fulfillment', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching order fulfillment:', error);
      throw error;
    }
  },

  getDeliveryMetrics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery-metrics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching delivery metrics:', error);
      throw error;
    }
  },

  // Financial Reports
  getCostOfGoods: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/cost-goods', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cost of goods:', error);
      throw error;
    }
  },

  getProfitMargins: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/profit-margins', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching profit margins:', error);
      throw error;
    }
  },

  getCommissionReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/commission', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching commission reports:', error);
      throw error;
    }
  },

  getWasteLoss: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/waste-loss', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching waste and loss:', error);
      throw error;
    }
  },

  getBreakEven: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/break-even', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching break-even analysis:', error);
      throw error;
    }
  },

  // Customer Experience Reports
  getOrderFulfillment: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/order-fulfillment', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching order fulfillment:', error);
      throw error;
    }
  },

  getDeliveryMetrics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery-metrics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching delivery metrics:', error);
      throw error;
    }
  },

  getCustomerFeedback: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/customer-feedback', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching customer feedback:', error);
      throw error;
    }
  },

  getUserEngagement: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/user-engagement', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching user engagement:', error);
      throw error;
    }
  },

  getLoyaltyProgram: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/loyalty-program', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching loyalty program:', error);
      throw error;
    }
  },

  // Financial Reports
  getCostOfGoods: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/cost-goods', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cost of goods:', error);
      throw error;
    }
  },

  getProfitMargins: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/profit-margins', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching profit margins:', error);
      throw error;
    }
  },

  getCommissionReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/commission', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching commission reports:', error);
      throw error;
    }
  },

  getWasteLoss: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/waste-loss', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching waste and loss:', error);
      throw error;
    }
  },

  getBreakEvenAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/break-even', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching break-even analysis:', error);
      throw error;
    }
  },

  // Classified App Reports
  getUserRegistration: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/user-registration', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching user registration:', error);
      throw error;
    }
  },

  getListingPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/listing-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching listing performance:', error);
      throw error;
    }
  },

  getCategoryAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/category-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching category analysis:', error);
      throw error;
    }
  },

  getEngagementMetrics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/engagement-metrics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching engagement metrics:', error);
      throw error;
    }
  },

  getUserRetention: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/user-retention', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching user retention:', error);
      throw error;
    }
  },

  // Revenue & Financial Reports
  getSubscriptionAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/subscription-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching subscription analytics:', error);
      throw error;
    }
  },

  getPremiumServiceUsage: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/premium-service', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching premium service usage:', error);
      throw error;
    }
  },

  getCommissionByCategory: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/commission-category', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching commission by category:', error);
      throw error;
    }
  },

  getPaymentProcessing: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/payment-processing', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching payment processing:', error);
      throw error;
    }
  },

  getCostPerAcquisition: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/cost-acquisition', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cost per acquisition:', error);
      throw error;
    }
  },

  // Content & Quality Reports
  getContentModeration: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/content-moderation', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching content moderation:', error);
      throw error;
    }
  },

  getListingQuality: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/listing-quality', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching listing quality:', error);
      throw error;
    }
  },

  getFraudDetection: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/fraud-detection', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching fraud detection:', error);
      throw error;
    }
  },

  getSearchAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/search-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching search analytics:', error);
      throw error;
    }
  },

  getUserGeneratedContent: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/ugc', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching user-generated content:', error);
      throw error;
    }
  },

  // Market Intelligence Reports
  getPriceAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/price-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching price analytics:', error);
      throw error;
    }
  },

  getDemandForecast: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/demand-forecast', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching demand forecast:', error);
      throw error;
    }
  },

  getGeographicPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/geographic-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching geographic performance:', error);
      throw error;
    }
  },

  getSeasonalTrends: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/seasonal-trends', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching seasonal trends:', error);
      throw error;
    }
  },

  getMarketSaturation: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/market-saturation', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching market saturation:', error);
      throw error;
    }
  },

  // Sales Performance Reports
  getSalesDashboard: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/sales-dashboard', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching sales dashboard:', error);
      throw error;
    }
  },

  getProductPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/product-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching product performance:', error);
      throw error;
    }
  },

  getCustomerLifetimeValue: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/customer-lifetime-value', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching customer lifetime value:', error);
      throw error;
    }
  },

  getSeasonalSalesAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/seasonal-sales-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching seasonal sales analysis:', error);
      throw error;
    }
  },

  getSalesChannelPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/sales-channel-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching sales channel performance:', error);
      throw error;
    }
  },

  // Inventory Management Reports
  getStockLevelReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/stock-level-reports', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching stock level reports:', error);
      throw error;
    }
  },

  getInventoryTurnover: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/inventory-turnover', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching inventory turnover:', error);
      throw error;
    }
  },

  getSupplierPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/supplier-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching supplier performance:', error);
      throw error;
    }
  },

  getDeadStockAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/dead-stock-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching dead stock analysis:', error);
      throw error;
    }
  },

  getInventoryDemandForecast: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/demand-forecasting', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching inventory demand forecast:', error);
      throw error;
    }
  },

  // Marketing & Customer Reports
  getConversionFunnel: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/conversion-funnel', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching conversion funnel:', error);
      throw error;
    }
  },

  getMarketingROI: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/marketing-roi', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching marketing ROI:', error);
      throw error;
    }
  },

  getCustomerSegmentation: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/customer-segmentation', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching customer segmentation:', error);
      throw error;
    }
  },

  getEmailMarketing: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/email-marketing', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching email marketing:', error);
      throw error;
    }
  },

  getSEOPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/seo-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching SEO performance:', error);
      throw error;
    }
  },

  // Financial & Operational Reports
  getProfitMargins: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/profit-margins', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching profit margins:', error);
      throw error;
    }
  },

  getShippingAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/shipping-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching shipping analytics:', error);
      throw error;
    }
  },

  getReturnRefundAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/return-refund-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching return/refund analysis:', error);
      throw error;
    }
  },

  getTaxCompliance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/tax-compliance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching tax compliance:', error);
      throw error;
    }
  },

  getPaymentProcessing: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/payment-processing', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching payment processing:', error);
      throw error;
    }
  },

  // Crypto P2P Marketplace Reports
  getVolumeAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/volume-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching volume analytics:', error);
      throw error;
    }
  },

  getPriceMovementAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/price-movement-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching price movement analysis:', error);
      throw error;
    }
  },

  getUserTradingBehavior: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/user-trading-behavior', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching user trading behavior:', error);
      throw error;
    }
  },

  getLiquidityReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/liquidity-reports', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching liquidity reports:', error);
      throw error;
    }
  },

  getOrderBookAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/order-book-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching order book analysis:', error);
      throw error;
    }
  },

  getKYCAMLCompliance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/kyc-aml-compliance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching KYC/AML compliance:', error);
      throw error;
    }
  },

  getRiskAssessment: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/risk-assessment', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching risk assessment:', error);
      throw error;
    }
  },

  getSecurityIncidents: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/security-incidents', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching security incidents:', error);
      throw error;
    }
  },

  getRegulatoryReporting: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/regulatory-reporting', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching regulatory reporting:', error);
      throw error;
    }
  },

  getAuditTrails: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/audit-trails', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching audit trails:', error);
      throw error;
    }
  },

  getFeeRevenue: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/fee-revenue', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching fee revenue:', error);
      throw error;
    }
  },

  getProfitMargins: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/profit-margins', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching profit margins:', error);
      throw error;
    }
  },

  getOperationalCosts: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/operational-costs', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching operational costs:', error);
      throw error;
    }
  },

  getCashFlowAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/cash-flow-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cash flow analysis:', error);
      throw error;
    }
  },

  getExchangeRateImpact: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/exchange-rate-impact', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching exchange rate impact:', error);
      throw error;
    }
  },

  getConsolidatedRevenue: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/consolidated-revenue', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching consolidated revenue:', error);
      throw error;
    }
  },

  getCrossPlatformCustomerAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/cross-platform-customer-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cross-platform customer analysis:', error);
      throw error;
    }
  },

  getSharedResourceUtilization: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/shared-resource-utilization', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching shared resource utilization:', error);
      throw error;
    }
  },

  getBudgetAllocation: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/budget-allocation', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching budget allocation:', error);
      throw error;
    }
  },

  getOverallBusinessPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/overall-business-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching overall business performance:', error);
      throw error;
    }
  },

  getCrossPlatformBehavior: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/cross-platform-behavior', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cross-platform behavior:', error);
      throw error;
    }
  },

  getUnifiedCustomerProfile: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/unified-customer-profile', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching unified customer profile:', error);
      throw error;
    }
  },

  getLifetimeValue: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/lifetime-value', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching lifetime value:', error);
      throw error;
    }
  },

  getChurnAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/churn-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching churn analysis:', error);
      throw error;
    }
  },

  getLoyaltyProgramPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/loyalty-program-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching loyalty program performance:', error);
      throw error;
    }
  },

  // Additional Cross-Platform Integration Reports
  getSharedInfrastructure: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/shared-infrastructure', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching shared infrastructure reports:', error);
      throw error;
    }
  },

  getStaffProductivity: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/staff-productivity', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching staff productivity:', error);
      throw error;
    }
  },

  getProcessStandardization: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/process-standardization', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching process standardization:', error);
      throw error;
    }
  },

  getTechnologyIntegration: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/technology-integration', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching technology integration:', error);
      throw error;
    }
  },

  getScalabilityAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/scalability-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching scalability analysis:', error);
      throw error;
    }
  },

  // Risk Management Reports
  getCrossPlatformRiskAssessment: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/cross-platform-risk-assessment', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cross-platform risk assessment:', error);
      throw error;
    }
  },

  getComplianceMonitoring: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/compliance-monitoring', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching compliance monitoring:', error);
      throw error;
    }
  },

  getFinancialRisk: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/financial-risk', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching financial risk:', error);
      throw error;
    }
  },

  getOperationalRisk: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/operational-risk', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching operational risk:', error);
      throw error;
    }
  },

  getMarketRisk: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/crypto-p2p/market-risk', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching market risk:', error);
      throw error;
    }
  }
};

export default reportService;