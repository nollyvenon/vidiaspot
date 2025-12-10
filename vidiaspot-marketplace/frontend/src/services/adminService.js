import apiClient from './api';

const adminService = {
  // Dashboard analytics
  getDashboardStats: async () => {
    try {
      const response = await apiClient.get('/admin/dashboard');
      return response.data;
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      throw error;
    }
  },

  // Get analytics data
  getAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching analytics:', error);
      throw error;
    }
  },

  // Manage ads
  getAds: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/ads', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching ads:', error);
      throw error;
    }
  },

  updateAdStatus: async (adId, status) => {
    try {
      const response = await apiClient.put(`/admin/ads/${adId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating ad status:', error);
      throw error;
    }
  },

  deleteAd: async (adId) => {
    try {
      const response = await apiClient.delete(`/admin/ads/${adId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting ad:', error);
      throw error;
    }
  },

  // Manage users
  getUsers: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/users', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching users:', error);
      throw error;
    }
  },

  updateUserRole: async (userId, role) => {
    try {
      const response = await apiClient.patch(`/admin/users/${userId}/role`, { role });
      return response.data;
    } catch (error) {
      console.error('Error updating user role:', error);
      throw error;
    }
  },

  suspendUser: async (userId) => {
    try {
      const response = await apiClient.patch(`/admin/users/${userId}/suspend`);
      return response.data;
    } catch (error) {
      console.error('Error suspending user:', error);
      throw error;
    }
  },

  // Manage categories
  getCategories: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/categories', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching categories:', error);
      throw error;
    }
  },

  createCategory: async (categoryData) => {
    try {
      const response = await apiClient.post('/admin/categories', categoryData);
      return response.data;
    } catch (error) {
      console.error('Error creating category:', error);
      throw error;
    }
  },

  updateCategory: async (categoryId, categoryData) => {
    try {
      const response = await apiClient.put(`/admin/categories/${categoryId}`, categoryData);
      return response.data;
    } catch (error) {
      console.error('Error updating category:', error);
      throw error;
    }
  },

  toggleCategoryStatus: async (categoryId) => {
    try {
      const response = await apiClient.patch(`/admin/categories/${categoryId}/toggle-status`);
      return response.data;
    } catch (error) {
      console.error('Error toggling category status:', error);
      throw error;
    }
  },

  // Manage payments
  getPayments: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/payments', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching payments:', error);
      throw error;
    }
  },

  updatePaymentStatus: async (paymentId, status) => {
    try {
      const response = await apiClient.put(`/admin/payments/${paymentId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating payment status:', error);
      throw error;
    }
  },

  // Manage vendors
  getVendors: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/vendors', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching vendors:', error);
      throw error;
    }
  },

  approveVendor: async (vendorId) => {
    try {
      const response = await apiClient.patch(`/admin/vendors/${vendorId}/approve`);
      return response.data;
    } catch (error) {
      console.error('Error approving vendor:', error);
      throw error;
    }
  },

  suspendVendor: async (vendorId) => {
    try {
      const response = await apiClient.patch(`/admin/vendors/${vendorId}/suspend`);
      return response.data;
    } catch (error) {
      console.error('Error suspending vendor:', error);
      throw error;
    }
  },

  // Manage featured ads
  getFeaturedAds: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/featured-ads', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching featured ads:', error);
      throw error;
    }
  },

  // Manage ad placements
  getAdPlacements: async (params = {}) => {
    try {
      const response = await apiClient.get('/admin/ad-placements', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching ad placements:', error);
      throw error;
    }
  },

  toggleAdPlacementStatus: async (placementId) => {
    try {
      const response = await apiClient.patch(`/admin/ad-placements/${placementId}/toggle-status`);
      return response.data;
    } catch (error) {
      console.error('Error toggling ad placement status:', error);
      throw error;
    }
  }
};

export default adminService;