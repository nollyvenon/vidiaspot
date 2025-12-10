import apiClient from './api';

const storeService = {
  // Get user's store
  getMyStore: async () => {
    try {
      const response = await apiClient.get('/stores/my');
      return response.data;
    } catch (error) {
      console.error('Error fetching store:', error);
      throw error;
    }
  },

  // Create a new store
  createStore: async (storeData) => {
    try {
      const response = await apiClient.post('/stores', storeData);
      return response.data;
    } catch (error) {
      console.error('Error creating store:', error);
      throw error;
    }
  },

  // Update store
  updateStore: async (storeId, storeData) => {
    try {
      const response = await apiClient.put(`/stores/${storeId}`, storeData);
      return response.data;
    } catch (error) {
      console.error('Error updating store:', error);
      throw error;
    }
  },

  // Get store by slug/public store
  getPublicStore: async (slug) => {
    try {
      const response = await apiClient.get(`/stores/${slug}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching public store:', error);
      throw error;
    }
  },

  // Manage custom domain
  addCustomDomain: async (storeId, domainData) => {
    try {
      const response = await apiClient.post(`/stores/${storeId}/domains`, domainData);
      return response.data;
    } catch (error) {
      console.error('Error adding custom domain:', error);
      throw error;
    }
  },

  // Verify domain ownership
  verifyDomain: async (storeId, domain) => {
    try {
      const response = await apiClient.post(`/stores/${storeId}/domains/verify`, { domain });
      return response.data;
    } catch (error) {
      console.error('Error verifying domain:', error);
      throw error;
    }
  },

  // Get store analytics
  getStoreAnalytics: async (storeId, params = {}) => {
    try {
      const response = await apiClient.get(`/stores/${storeId}/analytics`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching store analytics:', error);
      throw error;
    }
  },

  // Get store products
  getStoreProducts: async (storeId, params = {}) => {
    try {
      const response = await apiClient.get(`/stores/${storeId}/products`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching store products:', error);
      throw error;
    }
  },

  // Add product to store
  addProductToStore: async (storeId, productData) => {
    try {
      const response = await apiClient.post(`/stores/${storeId}/products`, productData);
      return response.data;
    } catch (error) {
      console.error('Error adding product to store:', error);
      throw error;
    }
  },

  // Get store orders
  getStoreOrders: async (storeId, params = {}) => {
    try {
      const response = await apiClient.get(`/stores/${storeId}/orders`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching store orders:', error);
      throw error;
    }
  },

  // Update store settings
  updateStoreSettings: async (storeId, settings) => {
    try {
      const response = await apiClient.put(`/stores/${storeId}/settings`, settings);
      return response.data;
    } catch (error) {
      console.error('Error updating store settings:', error);
      throw error;
    }
  },

  // Get store themes/templates
  getStoreThemes: async () => {
    try {
      const response = await apiClient.get('/store-themes');
      return response.data;
    } catch (error) {
      console.error('Error fetching store themes:', error);
      throw error;
    }
  },

  // Apply store theme
  applyStoreTheme: async (storeId, themeId) => {
    try {
      const response = await apiClient.put(`/stores/${storeId}/theme`, { theme_id: themeId });
      return response.data;
    } catch (error) {
      console.error('Error applying store theme:', error);
      throw error;
    }
  },

  // Get store customers
  getStoreCustomers: async (storeId, params = {}) => {
    try {
      const response = await apiClient.get(`/stores/${storeId}/customers`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching store customers:', error);
      throw error;
    }
  },

  // Get store payments
  getStorePayments: async (storeId, params = {}) => {
    try {
      const response = await apiClient.get(`/stores/${storeId}/payments`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching store payments:', error);
      throw error;
    }
  }
};

export default storeService;