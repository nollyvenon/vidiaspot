import apiClient from './api';

const foodVendorService = {
  // Get vendor's menu
  getVendorMenu: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/menu`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor menu:', error);
      throw error;
    }
  },

  // Get vendor's menu items
  getVendorMenuItems: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/menu-items`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor menu items:', error);
      throw error;
    }
  },

  // Add a new menu item
  addMenuItem: async (vendorId, menuItemData) => {
    try {
      const response = await apiClient.post(`/food-vendors/${vendorId}/menu-items`, menuItemData);
      return response.data;
    } catch (error) {
      console.error('Error adding menu item:', error);
      throw error;
    }
  },

  // Update a menu item
  updateMenuItem: async (vendorId, menuItemId, menuItemData) => {
    try {
      const response = await apiClient.put(`/food-vendors/${vendorId}/menu-items/${menuItemId}`, menuItemData);
      return response.data;
    } catch (error) {
      console.error('Error updating menu item:', error);
      throw error;
    }
  },

  // Delete a menu item
  deleteMenuItem: async (vendorId, menuItemId) => {
    try {
      const response = await apiClient.delete(`/food-vendors/${vendorId}/menu-items/${menuItemId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting menu item:', error);
      throw error;
    }
  },

  // Update menu item availability
  updateMenuItemAvailability: async (vendorId, menuItemId, availability) => {
    try {
      const response = await apiClient.put(`/food-vendors/${vendorId}/menu-items/${menuItemId}/availability`, { availability });
      return response.data;
    } catch (error) {
      console.error('Error updating menu item availability:', error);
      throw error;
    }
  },

  // Update menu item inventory
  updateMenuItemInventory: async (vendorId, menuItemId, inventory) => {
    try {
      const response = await apiClient.put(`/food-vendors/${vendorId}/menu-items/${menuItemId}/inventory`, { inventory });
      return response.data;
    } catch (error) {
      console.error('Error updating menu item inventory:', error);
      throw error;
    }
  },

  // Get vendor's daily specials
  getDailySpecials: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/daily-specials`);
      return response.data;
    } catch (error) {
      console.error('Error fetching daily specials:', error);
      throw error;
    }
  },

  // Add daily special
  addDailySpecial: async (vendorId, specialData) => {
    try {
      const response = await apiClient.post(`/food-vendors/${vendorId}/daily-specials`, specialData);
      return response.data;
    } catch (error) {
      console.error('Error adding daily special:', error);
      throw error;
    }
  },

  // Get vendor's categories
  getVendorCategories: async (vendorId) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/categories`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor categories:', error);
      throw error;
    }
  },

  // Add category
  addCategory: async (vendorId, categoryData) => {
    try {
      const response = await apiClient.post(`/food-vendors/${vendorId}/categories`, categoryData);
      return response.data;
    } catch (error) {
      console.error('Error adding category:', error);
      throw error;
    }
  },

  // Update category
  updateCategory: async (vendorId, categoryId, categoryData) => {
    try {
      const response = await apiClient.put(`/food-vendors/${vendorId}/categories/${categoryId}`, categoryData);
      return response.data;
    } catch (error) {
      console.error('Error updating category:', error);
      throw error;
    }
  },

  // Delete category
  deleteCategory: async (vendorId, categoryId) => {
    try {
      const response = await apiClient.delete(`/food-vendors/${vendorId}/categories/${categoryId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting category:', error);
      throw error;
    }
  },

  // Get vendor's order history
  getVendorOrderHistory: async (vendorId, params = {}) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/orders`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor order history:', error);
      throw error;
    }
  },

  // Get vendor analytics
  getVendorAnalytics: async (vendorId, params = {}) => {
    try {
      const response = await apiClient.get(`/food-vendors/${vendorId}/analytics`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching vendor analytics:', error);
      throw error;
    }
  }
};

export default foodVendorService;