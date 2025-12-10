import apiClient from './api';

const shopifyService = {
  // Get all connected Shopify stores
  getStores: async () => {
    try {
      const response = await apiClient.get('/shopify/stores');
      return response.data;
    } catch (error) {
      console.error('Error fetching Shopify stores:', error);
      throw error;
    }
  },

  // Connect a new Shopify store
  connectStore: async (storeData) => {
    try {
      const response = await apiClient.post('/shopify/stores', storeData);
      return response.data;
    } catch (error) {
      console.error('Error connecting Shopify store:', error);
      throw error;
    }
  },

  // Get products from a specific store
  getStoreProducts: async (storeId) => {
    try {
      const response = await apiClient.get(`/shopify/stores/${storeId}/products`);
      return response.data;
    } catch (error) {
      console.error('Error fetching store products:', error);
      throw error;
    }
  },

  // Get orders from a specific store
  getStoreOrders: async (storeId) => {
    try {
      const response = await apiClient.get(`/shopify/stores/${storeId}/orders`);
      return response.data;
    } catch (error) {
      console.error('Error fetching store orders:', error);
      throw error;
    }
  },

  // Sync products for a store
  syncProducts: async (storeId) => {
    try {
      const response = await apiClient.post(`/shopify/stores/${storeId}/sync-products`);
      return response.data;
    } catch (error) {
      console.error('Error syncing products:', error);
      throw error;
    }
  },

  // Sync orders for a store
  syncOrders: async (storeId) => {
    try {
      const response = await apiClient.post(`/shopify/stores/${storeId}/sync-orders`);
      return response.data;
    } catch (error) {
      console.error('Error syncing orders:', error);
      throw error;
    }
  },

  // Update a store's sync settings
  updateSyncSettings: async (storeId, settings) => {
    try {
      const response = await apiClient.put(`/shopify/stores/${storeId}/sync-settings`, settings);
      return response.data;
    } catch (error) {
      console.error('Error updating sync settings:', error);
      throw error;
    }
  },

  // Disconnect a store
  disconnectStore: async (storeId) => {
    try {
      const response = await apiClient.delete(`/shopify/stores/${storeId}`);
      return response.data;
    } catch (error) {
      console.error('Error disconnecting store:', error);
      throw error;
    }
  },

  // Get sync settings for a store
  getSyncSettings: async (storeId) => {
    try {
      const response = await apiClient.get(`/shopify/stores/${storeId}/sync-settings`);
      return response.data;
    } catch (error) {
      console.error('Error fetching sync settings:', error);
      throw error;
    }
  }
};

export default shopifyService;