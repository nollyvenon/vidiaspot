import apiClient from './api';

const importExportService = {
  // Get supported platforms
  getSupportedPlatforms: async () => {
    try {
      const response = await apiClient.get('/import-export/supported-platforms');
      return response.data;
    } catch (error) {
      console.error('Error fetching supported platforms:', error);
      throw error;
    }
  },

  // Get import status
  getImportStatus: async (importId) => {
    try {
      const response = await apiClient.get(`/import-export/${importId}/status`);
      return response.data;
    } catch (error) {
      console.error('Error fetching import status:', error);
      throw error;
    }
  },

  // Import from WooCommerce
  importFromWooCommerce: async (importData) => {
    try {
      const response = await apiClient.post('/import-export/woocommerce', importData);
      return response.data;
    } catch (error) {
      console.error('Error importing from WooCommerce:', error);
      throw error;
    }
  },

  // Import from Shopify
  importFromShopify: async (importData) => {
    try {
      const response = await apiClient.post('/import-export/shopify', importData);
      return response.data;
    } catch (error) {
      console.error('Error importing from Shopify:', error);
      throw error;
    }
  },

  // Import from BigCommerce
  importFromBigCommerce: async (importData) => {
    try {
      const response = await apiClient.post('/import-export/bigcommerce', importData);
      return response.data;
    } catch (error) {
      console.error('Error importing from BigCommerce:', error);
      throw error;
    }
  },

  // Import from Magento
  importFromMagento: async (importData) => {
    try {
      const response = await apiClient.post('/import-export/magento', importData);
      return response.data;
    } catch (error) {
      console.error('Error importing from Magento:', error);
      throw error;
    }
  },

  // Import from custom CSV
  importFromCSV: async (csvFile, importConfig) => {
    try {
      const formData = new FormData();
      formData.append('file', csvFile);
      Object.keys(importConfig).forEach(key => {
        formData.append(key, importConfig[key]);
      });

      const response = await apiClient.post('/import-export/csv', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      return response.data;
    } catch (error) {
      console.error('Error importing from CSV:', error);
      throw error;
    }
  },

  // Export to various formats
  exportToCSV: async (exportData) => {
    try {
      const response = await apiClient.post('/import-export/export-csv', exportData, {
        responseType: 'blob'
      });
      
      // Create download link
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `export-${Date.now()}.csv`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      
      return response.data;
    } catch (error) {
      console.error('Error exporting to CSV:', error);
      throw error;
    }
  },

  exportToShopify: async (exportData) => {
    try {
      const response = await apiClient.post('/import-export/export-shopify', exportData);
      return response.data;
    } catch (error) {
      console.error('Error exporting to Shopify:', error);
      throw error;
    }
  },

  exportToWooCommerce: async (exportData) => {
    try {
      const response = await apiClient.post('/import-export/export-woocommerce', exportData);
      return response.data;
    } catch (error) {
      console.error('Error exporting to WooCommerce:', error);
      throw error;
    }
  },

  // Get import/export history
  getHistory: async (params = {}) => {
    try {
      const response = await apiClient.get('/import-export/history', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching import/export history:', error);
      throw error;
    }
  },

  // Cancel an import/export
  cancelOperation: async (operationId) => {
    try {
      const response = await apiClient.post(`/import-export/${operationId}/cancel`);
      return response.data;
    } catch (error) {
      console.error('Error cancelling operation:', error);
      throw error;
    }
  },

  // Validate import file
  validateImportFile: async (file, platform) => {
    try {
      const formData = new FormData();
      formData.append('file', file);
      formData.append('platform', platform);

      const response = await apiClient.post('/import-export/validate', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      return response.data;
    } catch (error) {
      console.error('Error validating import file:', error);
      throw error;
    }
  },

  // Get mapping options for import
  getImportMappingOptions: async (platform) => {
    try {
      const response = await apiClient.get(`/import-export/${platform}/mapping-options`);
      return response.data;
    } catch (error) {
      console.error('Error fetching mapping options:', error);
      throw error;
    }
  },

  // Preview import data
  previewImport: async (previewData) => {
    try {
      const response = await apiClient.post('/import-export/preview', previewData);
      return response.data;
    } catch (error) {
      console.error('Error previewing import:', error);
      throw error;
    }
  },

  // Import customers
  importCustomers: async (importData) => {
    try {
      const response = await apiClient.post('/import-export/customers', importData);
      return response.data;
    } catch (error) {
      console.error('Error importing customers:', error);
      throw error;
    }
  },

  // Import orders
  importOrders: async (importData) => {
    try {
      const response = await apiClient.post('/import-export/orders', importData);
      return response.data;
    } catch (error) {
      console.error('Error importing orders:', error);
      throw error;
    }
  },

  // Import categories
  importCategories: async (importData) => {
    try {
      const response = await apiClient.post('/import-export/categories', importData);
      return response.data;
    } catch (error) {
      console.error('Error importing categories:', error);
      throw error;
    }
  }
};

export default importExportService;