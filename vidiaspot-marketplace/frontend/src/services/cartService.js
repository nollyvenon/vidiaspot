import apiClient from './api';

const cartService = {
  // Get user's cart
  getCart: async () => {
    try {
      const response = await apiClient.get('/cart');
      return response.data;
    } catch (error) {
      console.error('Error fetching cart:', error);
      throw error;
    }
  },

  // Add item to cart
  addToCart: async (productId, quantity = 1, variantId = null) => {
    try {
      const response = await apiClient.post('/cart/add', {
        product_id: productId,
        quantity,
        variant_id: variantId
      });
      return response.data;
    } catch (error) {
      console.error('Error adding to cart:', error);
      throw error;
    }
  },

  // Update cart item
  updateCartItem: async (productId, quantity, variantId = null) => {
    try {
      const response = await apiClient.put(`/cart/${productId}`, {
        quantity,
        variant_id: variantId
      });
      return response.data;
    } catch (error) {
      console.error('Error updating cart item:', error);
      throw error;
    }
  },

  // Remove item from cart
  removeFromCart: async (productId, variantId = null) => {
    try {
      const endpoint = variantId ? `/cart/${productId}?variant_id=${variantId}` : `/cart/${productId}`;
      const response = await apiClient.delete(endpoint);
      return response.data;
    } catch (error) {
      console.error('Error removing from cart:', error);
      throw error;
    }
  },

  // Clear cart
  clearCart: async () => {
    try {
      const response = await apiClient.delete('/cart');
      return response.data;
    } catch (error) {
      console.error('Error clearing cart:', error);
      throw error;
    }
  },

  // Checkout cart
  checkout: async (checkoutData) => {
    try {
      const response = await apiClient.post('/cart/checkout', checkoutData);
      return response.data;
    } catch (error) {
      console.error('Error checking out:', error);
      throw error;
    }
  },

  // Apply discount code
  applyDiscountCode: async (code) => {
    try {
      const response = await apiClient.post('/cart/discount', { code });
      return response.data;
    } catch (error) {
      console.error('Error applying discount:', error);
      throw error;
    }
  }
};

export default cartService;