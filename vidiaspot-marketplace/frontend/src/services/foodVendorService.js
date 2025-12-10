import apiClient from './api';

const foodVendorService = {
  // Get all food vendors
  getVendors: async (params = {}) => {
    try {
      const response = await apiClient.get('/food-vendors', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching food vendors:', error);
      throw error;
    }
  },

  // Get vendor by ID
  getVendor: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor:', error);
      throw error;
    }
  },

  // Search vendors
  searchVendors: async (query, params = {}) => {
    try {
      const response = await apiClient.get('/food-vendors/search', {
        params: { ...params, q: query }
      });
      return response.data;
    } catch (error) {
      console.error('Error searching vendors:', error);
      throw error;
    }
  },

  // Get vendor menu
  getVendorMenu: async (vendorId, params = {}) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/menu`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor menu:', error);
      throw error;
    }
  },

  // Get menu by category
  getVendorMenuByCategory: async (vendorId, category) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/menu/category/${category}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor menu by category:', error);
      throw error;
    }
  },

  // Get popular menu items
  getPopularMenuItems: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/menu/popular`);
      return response.data;
    } catch (error) {
      console.error('Error fetching popular menu items:', error);
      throw error;
    }
  },

  // Place order
  placeOrder: async (orderData) => {
    try {
      const response = await apiClient.post('/food-orders', orderData);
      return response.data;
    } catch (error) {
      console.error('Error placing order:', error);
      throw error;
    }
  },

  // Get user's order history
  getOrderHistory: async (params = {}) => {
    try {
      const response = await apiClient.get('/food-orders', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching order history:', error);
      throw error;
    }
  },

  // Get specific order
  getOrder: async (orderNumber) => {
    try {
      const response = await apiClient.get(`/food-orders/${orderNumber}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching order:', error);
      throw error;
    }
  },

  // Update order status
  updateOrderStatus: async (orderNumber, status) => {
    try {
      const response = await apiClient.put(`/food-orders/${orderNumber}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating order status:', error);
      throw error;
    }
  },

  // Get vendor stats
  getVendorStats: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/stats`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor stats:', error);
      throw error;
    }
  },

  // Get vendors by cuisine
  getVendorsByCuisine: async (cuisineType, params = {}) => {
    try {
      const response = await apiClient.get(`/food-vendors/cuisine/${cuisineType}`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching vendors by cuisine:', error);
      throw error;
    }
  },

  // Get delivery zones
  getDeliveryZones: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/delivery-zones`);
      return response.data;
    } catch (error) {
      console.error('Error fetching delivery zones:', error);
      throw error;
    }
  },

  // Check delivery availability
  checkDeliveryAvailability: async (vendorId, address) => {
    try {
      const response = await apiClient.post(`/food-vendors/${vendorId}/check-delivery`, { address });
      return response.data;
    } catch (error) {
      console.error('Error checking delivery availability:', error);
      throw error;
    }
  }
};

export default foodVendorService;