import apiClient from './api';

const foodVendingService = {
  // Get all vending machines
  getVendingMachines: async () => {
    try {
      const response = await apiClient.get('/food-vending/machines');
      return response.data;
    } catch (error) {
      console.error('Error fetching vending machines:', error);
      throw error;
    }
  },

  // Get vending machine by ID
  getVendingMachine: async (machineId) => {
    try {
      const response = await apiClient.get(`/food-vending/machines/${machineId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vending machine:', error);
      throw error;
    }
  },

  // Create a new vending machine
  createVendingMachine: async (machineData) => {
    try {
      const response = await apiClient.post('/food-vending/machines', machineData);
      return response.data;
    } catch (error) {
      console.error('Error creating vending machine:', error);
      throw error;
    }
  },

  // Update a vending machine
  updateVendingMachine: async (machineId, machineData) => {
    try {
      const response = await apiClient.put(`/food-vending/machines/${machineId}`, machineData);
      return response.data;
    } catch (error) {
      console.error('Error updating vending machine:', error);
      throw error;
    }
  },

  // Delete a vending machine
  deleteVendingMachine: async (machineId) => {
    try {
      const response = await apiClient.delete(`/food-vending/machines/${machineId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting vending machine:', error);
      throw error;
    }
  },

  // Get inventory for a vending machine
  getMachineInventory: async (machineId) => {
    try {
      const response = await apiClient.get(`/food-vending/machines/${machineId}/inventory`);
      return response.data;
    } catch (error) {
      console.error('Error fetching machine inventory:', error);
      throw error;
    }
  },

  // Update inventory for a vending machine
  updateMachineInventory: async (machineId, inventoryData) => {
    try {
      const response = await apiClient.put(`/food-vending/machines/${machineId}/inventory`, inventoryData);
      return response.data;
    } catch (error) {
      console.error('Error updating machine inventory:', error);
      throw error;
    }
  },

  // Add items to machine inventory
  addInventoryItems: async (machineId, itemsData) => {
    try {
      const response = await apiClient.post(`/food-vending/machines/${machineId}/inventory`, itemsData);
      return response.data;
    } catch (error) {
      console.error('Error adding inventory items:', error);
      throw error;
    }
  },

  // Get analytics for vending machines
  getAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/food-vending/analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching analytics:', error);
      throw error;
    }
  },

  // Get revenue overview
  getRevenueOverview: async (params = {}) => {
    try {
      const response = await apiClient.get('/food-vending/revenue', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching revenue data:', error);
      throw error;
    }
  },

  // Get top selling items
  getTopSellingItems: async (params = {}) => {
    try {
      const response = await apiClient.get('/food-vending/top-selling', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching top selling items:', error);
      throw error;
    }
  },

  // Update machine status
  updateMachineStatus: async (machineId, status) => {
    try {
      const response = await apiClient.patch(`/food-vending/machines/${machineId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating machine status:', error);
      throw error;
    }
  }
};

export default foodVendingService;