import apiClient from './api';

const ecommerceService = {
  // Website Builder & Templates
  getTemplates: async () => {
    try {
      const response = await apiClient.get('/ecommerce/templates');
      return response.data;
    } catch (error) {
      console.error('Error fetching templates:', error);
      throw error;
    }
  },

  createWebsite: async (websiteData) => {
    try {
      const response = await apiClient.post('/ecommerce/websites', websiteData);
      return response.data;
    } catch (error) {
      console.error('Error creating website:', error);
      throw error;
    }
  },

  updateWebsite: async (websiteId, websiteData) => {
    try {
      const response = await apiClient.put(`/ecommerce/websites/${websiteId}`, websiteData);
      return response.data;
    } catch (error) {
      console.error('Error updating website:', error);
      throw error;
    }
  },

  getWebsite: async (websiteId) => {
    try {
      const response = await apiClient.get(`/ecommerce/websites/${websiteId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching website:', error);
      throw error;
    }
  },

  // Product Management
  getProducts: async (params = {}) => {
    try {
      const response = await apiClient.get('/ecommerce/products', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching products:', error);
      throw error;
    }
  },

  getProduct: async (productId) => {
    try {
      const response = await apiClient.get(`/ecommerce/products/${productId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching product:', error);
      throw error;
    }
  },

  createProduct: async (productData) => {
    try {
      const response = await apiClient.post('/ecommerce/products', productData);
      return response.data;
    } catch (error) {
      console.error('Error creating product:', error);
      throw error;
    }
  },

  updateProduct: async (productId, productData) => {
    try {
      const response = await apiClient.put(`/ecommerce/products/${productId}`, productData);
      return response.data;
    } catch (error) {
      console.error('Error updating product:', error);
      throw error;
    }
  },

  deleteProduct: async (productId) => {
    try {
      const response = await apiClient.delete(`/ecommerce/products/${productId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting product:', error);
      throw error;
    }
  },

  // Product Variants & Inventory
  getProductVariants: async (productId) => {
    try {
      const response = await apiClient.get(`/ecommerce/products/${productId}/variants`);
      return response.data;
    } catch (error) {
      console.error('Error fetching product variants:', error);
      throw error;
    }
  },

  createProductVariant: async (productId, variantData) => {
    try {
      const response = await apiClient.post(`/ecommerce/products/${productId}/variants`, variantData);
      return response.data;
    } catch (error) {
      console.error('Error creating product variant:', error);
      throw error;
    }
  },

  updateInventory: async (productId, inventoryData) => {
    try {
      const response = await apiClient.put(`/ecommerce/products/${productId}/inventory`, inventoryData);
      return response.data;
    } catch (error) {
      console.error('Error updating inventory:', error);
      throw error;
    }
  },

  // Payment Gateway Integration
  getPaymentGateways: async () => {
    try {
      const response = await apiClient.get('/ecommerce/payment-gateways');
      return response.data;
    } catch (error) {
      console.error('Error fetching payment gateways:', error);
      throw error;
    }
  },

  configurePaymentGateway: async (gatewayData) => {
    try {
      const response = await apiClient.post('/ecommerce/payment-gateways/configure', gatewayData);
      return response.data;
    } catch (error) {
      console.error('Error configuring payment gateway:', error);
      throw error;
    }
  },

  getPaymentMethods: async () => {
    try {
      const response = await apiClient.get('/ecommerce/payment-methods');
      return response.data;
    } catch (error) {
      console.error('Error fetching payment methods:', error);
      throw error;
    }
  },

  // SEO Tools
  updateProductSeo: async (productId, seoData) => {
    try {
      const response = await apiClient.put(`/ecommerce/products/${productId}/seo`, seoData);
      return response.data;
    } catch (error) {
      console.error('Error updating product SEO:', error);
      throw error;
    }
  },

  generateSitemap: async (storeId) => {
    try {
      const response = await apiClient.get(`/ecommerce/stores/${storeId}/sitemap`);
      return response.data;
    } catch (error) {
      console.error('Error generating sitemap:', error);
      throw error;
    }
  },

  // Multi-language & Multi-currency
  getSupportedLanguages: async () => {
    try {
      const response = await apiClient.get('/ecommerce/languages');
      return response.data;
    } catch (error) {
      console.error('Error fetching supported languages:', error);
      throw error;
    }
  },

  getSupportedCurrencies: async () => {
    try {
      const response = await apiClient.get('/ecommerce/currencies');
      return response.data;
    } catch (error) {
      console.error('Error fetching supported currencies:', error);
      throw error;
    }
  },

  updateStoreLanguage: async (storeId, languageData) => {
    try {
      const response = await apiClient.put(`/ecommerce/stores/${storeId}/language`, languageData);
      return response.data;
    } catch (error) {
      console.error('Error updating store language:', error);
      throw error;
    }
  },

  updateStoreCurrency: async (storeId, currencyData) => {
    try {
      const response = await apiClient.put(`/ecommerce/stores/${storeId}/currency`, currencyData);
      return response.data;
    } catch (error) {
      console.error('Error updating store currency:', error);
      throw error;
    }
  },

  // Security & Compliance
  getSSLStatus: async (storeId) => {
    try {
      const response = await apiClient.get(`/ecommerce/stores/${storeId}/ssl`);
      return response.data;
    } catch (error) {
      console.error('Error fetching SSL status:', error);
      throw error;
    }
  },

  enableSSL: async (storeId) => {
    try {
      const response = await apiClient.post(`/ecommerce/stores/${storeId}/ssl/enable`);
      return response.data;
    } catch (error) {
      console.error('Error enabling SSL:', error);
      throw error;
    }
  },

  getPCIComplianceStatus: async (storeId) => {
    try {
      const response = await apiClient.get(`/ecommerce/stores/${storeId}/pci-compliance`);
      return response.data;
    } catch (error) {
      console.error('Error fetching PCI compliance status:', error);
      throw error;
    }
  },

  // Bulk Operations
  bulkImportProducts: async (csvData, storeId) => {
    try {
      const response = await apiClient.post(`/ecommerce/stores/${storeId}/products/bulk-import`, csvData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      return response.data;
    } catch (error) {
      console.error('Error importing products:', error);
      throw error;
    }
  },

  exportProducts: async (storeId, params = {}) => {
    try {
      const response = await apiClient.get(`/ecommerce/stores/${storeId}/products/export`, { 
        params,
        responseType: 'blob' 
      });
      
      // Create download link for exported file
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `products-export-${Date.now()}.csv`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      
      return response.data;
    } catch (error) {
      console.error('Error exporting products:', error);
      throw error;
    }
  }
};

export default ecommerceService;