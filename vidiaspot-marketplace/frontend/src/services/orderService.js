import apiClient from './api';

const orderService = {
  // Get user's orders
  getUserOrders: async (params = {}) => {
    try {
      const response = await apiClient.get('/orders', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching user orders:', error);
      throw error;
    }
  },

  // Get order by ID
  getOrder: async (orderId) => {
    try {
      const response = await apiClient.get(`/orders/${orderId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching order:', error);
      throw error;
    }
  },

  // Create a new order
  createOrder: async (orderData) => {
    try {
      const response = await apiClient.post('/orders', orderData);
      return response.data;
    } catch (error) {
      console.error('Error creating order:', error);
      throw error;
    }
  },

  // Update order status
  updateOrderStatus: async (orderId, status) => {
    try {
      const response = await apiClient.put(`/orders/${orderId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating order status:', error);
      throw error;
    }
  },

  // Cancel order
  cancelOrder: async (orderId) => {
    try {
      const response = await apiClient.delete(`/orders/${orderId}`);
      return response.data;
    } catch (error) {
      console.error('Error cancelling order:', error);
      throw error;
    }
  },

  // Get order tracking information
  getOrderTracking: async (orderId) => {
    try {
      const response = await apiClient.get(`/orders/${orderId}/tracking`);
      return response.data;
    } catch (error) {
      console.error('Error fetching order tracking:', error);
      throw error;
    }
  },

  // Get order analytics
  getOrderAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/orders/analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching order analytics:', error);
      throw error;
    }
  },

  // Get order statistics
  getOrderStats: async (params = {}) => {
    try {
      const response = await apiClient.get('/orders/stats', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching order stats:', error);
      throw error;
    }
  }
};

export default orderService;