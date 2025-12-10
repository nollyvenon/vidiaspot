import apiClient from './api';

const inventoryOrderService = {
  // Inventory Management
  getInventoryLevels: async (params = {}) => {
    try {
      const response = await apiClient.get('/inventory/levels', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching inventory levels:', error);
      throw error;
    }
  },

  getLowStockAlerts: async (params = {}) => {
    try {
      const response = await apiClient.get('/inventory/low-stock-alerts', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching low stock alerts:', error);
      throw error;
    }
  },

  setReorderPoints: async (productId, threshold) => {
    try {
      const response = await apiClient.post(`/inventory/${productId}/reorder-point`, { threshold });
      return response.data;
    } catch (error) {
      console.error('Error setting reorder point:', error);
      throw error;
    }
  },

  updateBulkInventory: async (inventoryUpdates) => {
    try {
      const response = await apiClient.put('/inventory/bulk-update', inventoryUpdates);
      return response.data;
    } catch (error) {
      console.error('Error updating bulk inventory:', error);
      throw error;
    }
  },

  // Order Management
  getOrders: async (params = {}) => {
    try {
      const response = await apiClient.get('/orders', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching orders:', error);
      throw error;
    }
  },

  getOrder: async (orderId) => {
    try {
      const response = await apiClient.get(`/orders/${orderId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching order:', error);
      throw error;
    }
  },

  createOrder: async (orderData) => {
    try {
      const response = await apiClient.post('/orders', orderData);
      return response.data;
    } catch (error) {
      console.error('Error creating order:', error);
      throw error;
    }
  },

  updateOrderStatus: async (orderId, status) => {
    try {
      const response = await apiClient.put(`/orders/${orderId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating order status:', error);
      throw error;
    }
  },

  generateShippingLabel: async (orderId) => {
    try {
      const response = await apiClient.post(`/orders/${orderId}/shipping-label`);
      return response.data;
    } catch (error) {
      console.error('Error generating shipping label:', error);
      throw error;
    }
  },

  // RMA (Return Merchandise Authorization)
  createRMA: async (rmaData) => {
    try {
      const response = await apiClient.post('/rma', rmaData);
      return response.data;
    } catch (error) {
      console.error('Error creating RMA:', error);
      throw error;
    }
  },

  getRMAs: async (params = {}) => {
    try {
      const response = await apiClient.get('/rma', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching RMAs:', error);
      throw error;
    }
  },

  updateRMAStatus: async (rmaId, status) => {
    try {
      const response = await apiClient.put(`/rma/${rmaId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating RMA status:', error);
      throw error;
    }
  },

  // Dropshipping Support
  getDropshippers: async (params = {}) => {
    try {
      const response = await apiClient.get('/dropshipping/suppliers', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching dropshippers:', error);
      throw error;
    }
  },

  createDropshippingOrder: async (orderData) => {
    try {
      const response = await apiClient.post('/dropshipping/orders', orderData);
      return response.data;
    } catch (error) {
      console.error('Error creating dropshipping order:', error);
      throw error;
    }
  },

  getDropshippingOrder: async (orderId) => {
    try {
      const response = await apiClient.get(`/dropshipping/orders/${orderId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching dropshipping order:', error);
      throw error;
    }
  },

  // Consignment Management
  getConsignmentProducts: async (params = {}) => {
    try {
      const response = await apiClient.get('/consignment/products', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching consignment products:', error);
      throw error;
    }
  },

  createConsignment: async (consignmentData) => {
    try {
      const response = await apiClient.post('/consignment', consignmentData);
      return response.data;
    } catch (error) {
      console.error('Error creating consignment:', error);
      throw error;
    }
  },

  updateConsignmentStatus: async (consignmentId, status) => {
    try {
      const response = await apiClient.put(`/consignment/${consignmentId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating consignment status:', error);
      throw error;
    }
  },

  getConsignmentReport: async (params = {}) => {
    try {
      const response = await apiClient.get('/consignment/report', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching consignment report:', error);
      throw error;
    }
  },

  // Multi-channel Inventory Sync
  getChannelInventory: async (channelId, params = {}) => {
    try {
      const response = await apiClient.get(`/inventory/channels/${channelId}`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching channel inventory:', error);
      throw error;
    }
  },

  syncChannelInventory: async (channelId, syncData) => {
    try {
      const response = await apiClient.post(`/inventory/channels/${channelId}/sync`, syncData);
      return response.data;
    } catch (error) {
      console.error('Error syncing channel inventory:', error);
      throw error;
    }
  },

  // Automated Reordering
  getReorderRecommendations: async (params = {}) => {
    try {
      const response = await apiClient.get('/inventory/reorder-recommendations', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching reorder recommendations:', error);
      throw error;
    }
  },

  createPurchaseOrder: async (poData) => {
    try {
      const response = await apiClient.post('/inventory/purchase-orders', poData);
      return response.data;
    } catch (error) {
      console.error('Error creating purchase order:', error);
      throw error;
    }
  },

  getPurchaseOrders: async (params = {}) => {
    try {
      const response = await apiClient.get('/inventory/purchase-orders', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching purchase orders:', error);
      throw error;
    }
  },

  updatePurchaseOrderStatus: async (poId, status) => {
    try {
      const response = await apiClient.put(`/inventory/purchase-orders/${poId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating purchase order status:', error);
      throw error;
    }
  }
};

export default inventoryOrderService;