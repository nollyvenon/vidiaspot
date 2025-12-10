import apiClient from './api';

const salesChannelService = {
  // Marketplace Integrations
  getIntegrations: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/integrations', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching integrations:', error);
      throw error;
    }
  },

  connectIntegration: async (integrationData) => {
    try {
      const response = await apiClient.post('/sales-channels/integrations', integrationData);
      return response.data;
    } catch (error) {
      console.error('Error connecting integration:', error);
      throw error;
    }
  },

  disconnectIntegration: async (integrationId) => {
    try {
      const response = await apiClient.delete(`/sales-channels/integrations/${integrationId}`);
      return response.data;
    } catch (error) {
      console.error('Error disconnecting integration:', error);
      throw error;
    }
  },

  getAmazonListings: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/amazon/listings', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching Amazon listings:', error);
      throw error;
    }
  },

  syncToAmazon: async (syncData) => {
    try {
      const response = await apiClient.post('/sales-channels/amazon/sync', syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing to Amazon:', error);
      throw error;
    }
  },

  geteBayListings: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/ebay/listings', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching eBay listings:', error);
      throw error;
    }
  },

  syncToeBay: async (syncData) => {
    try {
      const response = await apiClient.post('/sales-channels/ebay/sync', syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing to eBay:', error);
      throw error;
    }
  },

  getFacebookListings: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/facebook/listings', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching Facebook listings:', error);
      throw error;
    }
  },

  syncToFacebook: async (syncData) => {
    try {
      const response = await apiClient.post('/sales-channels/facebook/sync', syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing to Facebook:', error);
      throw error;
    }
  },

  getInstagramListings: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/instagram/listings', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching Instagram listings:', error);
      throw error;
    }
  },

  syncToInstagram: async (syncData) => {
    try {
      const response = await apiClient.post('/sales-channels/instagram/sync', syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing to Instagram:', error);
      throw error;
    }
  },

  getTikTokListings: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/tiktok/listings', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching TikTok listings:', error);
      throw error;
    }
  },

  syncToTikTok: async (syncData) => {
    try {
      const response = await apiClient.post('/sales-channels/tiktok/sync', syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing to TikTok:', error);
      throw error;
    }
  },

  // POS System Integration
  getPOSConfig: async (storeId) => {
    try {
      const response = await apiClient.get(`/sales-channels/${storeId}/pos-config`);
      return response.data;
    } catch (error) {
      console.error('Error fetching POS config:', error);
      throw error;
    }
  },

  configurePOS: async (storeId, posData) => {
    try {
      const response = await apiClient.post(`/sales-channels/${storeId}/pos-config`, posData);
      return response.data;
    } catch (error) {
      console.error('Error configuring POS:', error);
      throw error;
    }
  },

  syncPOSInventory: async (storeId, syncData) => {
    try {
      const response = await apiClient.post(`/sales-channels/${storeId}/pos-sync`, syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing POS inventory:', error);
      throw error;
    }
  },

  // Wholesale Channel
  getWholesaleConfig: async (storeId) => {
    try {
      const response = await apiClient.get(`/sales-channels/${storeId}/wholesale-config`);
      return response.data;
    } catch (error) {
      console.error('Error fetching wholesale config:', error);
      throw error;
    }
  },

  configureWholesale: async (storeId, wholesaleData) => {
    try {
      const response = await apiClient.post(`/sales-channels/${storeId}/wholesale-config`, wholesaleData);
      return response.data;
    } catch (error) {
      console.error('Error configuring wholesale:', error);
      throw error;
    }
  },

  getWholesaleCustomers: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/wholesale-customers', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching wholesale customers:', error);
      throw error;
    }
  },

  createWholesaleCustomer: async (customerData) => {
    try {
      const response = await apiClient.post('/sales-channels/wholesale-customers', customerData);
      return response.data;
    } catch (error) {
      console.error('Error creating wholesale customer:', error);
      throw error;
    }
  },

  // Buy Button Integration
  getBuyButtons: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/buy-buttons', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching buy buttons:', error);
      throw error;
    }
  },

  createBuyButton: async (buttonData) => {
    try {
      const response = await apiClient.post('/sales-channels/buy-buttons', buttonData);
      return response.data;
    } catch (error) {
      console.error('Error creating buy button:', error);
      throw error;
    }
  },

  updateBuyButton: async (buttonId, buttonData) => {
    try {
      const response = await apiClient.put(`/sales-channels/buy-buttons/${buttonId}`, buttonData);
      return response.data;
    } catch (error) {
      console.error('Error updating buy button:', error);
      throw error;
    }
  },

  deleteBuyButton: async (buttonId) => {
    try {
      const response = await apiClient.delete(`/sales-channels/buy-buttons/${buttonId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting buy button:', error);
      throw error;
    }
  },

  // Subscription Management
  getSubscriptionPlans: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/subscriptions/plans', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching subscription plans:', error);
      throw error;
    }
  },

  createSubscriptionPlan: async (planData) => {
    try {
      const response = await apiClient.post('/sales-channels/subscriptions/plans', planData);
      return response.data;
    } catch (error) {
      console.error('Error creating subscription plan:', error);
      throw error;
    }
  },

  getSubscriptions: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/subscriptions', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching subscriptions:', error);
      throw error;
    }
  },

  createSubscription: async (subscriptionData) => {
    try {
      const response = await apiClient.post('/sales-channels/subscriptions', subscriptionData);
      return response.data;
    } catch (error) {
      console.error('Error creating subscription:', error);
      throw error;
    }
  },

  updateSubscriptionStatus: async (subscriptionId, status) => {
    try {
      const response = await apiClient.put(`/sales-channels/subscriptions/${subscriptionId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating subscription status:', error);
      throw error;
    }
  },

  // Channel Performance Analytics
  getChannelPerformance: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching channel performance:', error);
      throw error;
    }
  },

  getCrossChannelInventory: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/inventory-sync', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cross-channel inventory:', error);
      throw error;
    }
  },

  syncAllChannels: async (syncData) => {
    try {
      const response = await apiClient.post('/sales-channels/sync-all', syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing all channels:', error);
      throw error;
    }
  },

  getChannelOrders: async (channelId, params = {}) => {
    try {
      const response = await apiClient.get(`/sales-channels/${channelId}/orders`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching channel orders:', error);
      throw error;
    }
  },

  getChannelRevenue: async (params = {}) => {
    try {
      const response = await apiClient.get('/sales-channels/revenue', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching channel revenue:', error);
      throw error;
    }
  }
};

export default salesChannelService;