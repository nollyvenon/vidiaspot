import apiClient from './api';

const productService = {
  // Get all products
  getProducts: async (params = {}) => {
    try {
      const response = await apiClient.get('/products', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching products:', error);
      throw error;
    }
  },

  // Get product by ID
  getProduct: async (productId) => {
    try {
      const response = await apiClient.get(`/products/${productId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching product:', error);
      throw error;
    }
  },

  // Create a new product
  createProduct: async (productData) => {
    try {
      const response = await apiClient.post('/products', productData);
      return response.data;
    } catch (error) {
      console.error('Error creating product:', error);
      throw error;
    }
  },

  // Update a product
  updateProduct: async (productId, productData) => {
    try {
      const response = await apiClient.put(`/products/${productId}`, productData);
      return response.data;
    } catch (error) {
      console.error('Error updating product:', error);
      throw error;
    }
  },

  // Delete a product
  deleteProduct: async (productId) => {
    try {
      const response = await apiClient.delete(`/products/${productId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting product:', error);
      throw error;
    }
  },

  // Update product inventory
  updateInventory: async (productId, inventoryData) => {
    try {
      const response = await apiClient.put(`/products/${productId}/inventory`, inventoryData);
      return response.data;
    } catch (error) {
      console.error('Error updating inventory:', error);
      throw error;
    }
  },

  // Get product variants
  getProductVariants: async (productId) => {
    try {
      const response = await apiClient.get(`/products/${productId}/variants`);
      return response.data;
    } catch (error) {
      console.error('Error fetching product variants:', error);
      throw error;
    }
  },

  // Create product variant
  createProductVariant: async (productId, variantData) => {
    try {
      const response = await apiClient.post(`/products/${productId}/variants`, variantData);
      return response.data;
    } catch (error) {
      console.error('Error creating product variant:', error);
      throw error;
    }
  },

  // Get product analytics
  getProductAnalytics: async (productId, params = {}) => {
    try {
      const response = await apiClient.get(`/products/${productId}/analytics`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching product analytics:', error);
      throw error;
    }
  },

  // Get product recommendations
  getProductRecommendations: async (params = {}) => {
    try {
      const response = await apiClient.get('/products/recommendations', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching product recommendations:', error);
      throw error;
    }
  }
};

export default productService;