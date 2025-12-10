import apiClient from './api';

const clientService = {
  // Client Profile Management
  getClients: async (params = {}) => {
    try {
      const response = await apiClient.get('/clients', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching clients:', error);
      throw error;
    }
  },

  getClient: async (clientId) => {
    try {
      const response = await apiClient.get(`/clients/${clientId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching client:', error);
      throw error;
    }
  },

  createClient: async (clientData) => {
    try {
      const response = await apiClient.post('/clients', clientData);
      return response.data;
    } catch (error) {
      console.error('Error creating client:', error);
      throw error;
    }
  },

  updateClient: async (clientId, clientData) => {
    try {
      const response = await apiClient.put(`/clients/${clientId}`, clientData);
      return response.data;
    } catch (error) {
      console.error('Error updating client:', error);
      throw error;
    }
  },

  deleteClient: async (clientId) => {
    try {
      const response = await apiClient.delete(`/clients/${clientId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting client:', error);
      throw error;
    }
  },

  // SLA Management
  getSlaForClient: async (clientId) => {
    try {
      const response = await apiClient.get(`/clients/${clientId}/sla`);
      return response.data;
    } catch (error) {
      console.error('Error fetching SLA for client:', error);
      throw error;
    }
  },

  updateSlaForClient: async (clientId, slaData) => {
    try {
      const response = await apiClient.put(`/clients/${clientId}/sla`, slaData);
      return response.data;
    } catch (error) {
      console.error('Error updating SLA for client:', error);
      throw error;
    }
  },

  // Credit Management  
  getCreditInfo: async (clientId) => {
    try {
      const response = await apiClient.get(`/clients/${clientId}/credit`);
      return response.data;
    } catch (error) {
      console.error('Error fetching credit info:', error);
      throw error;
    }
  },

  updateCreditTerms: async (clientId, creditData) => {
    try {
      const response = await apiClient.put(`/clients/${clientId}/credit`, creditData);
      return response.data;
    } catch (error) {
      console.error('Error updating credit terms:', error);
      throw error;
    }
  },

  // Delivery Logging System
  logDelivery: async (deliveryData) => {
    try {
      const response = await apiClient.post('/deliveries', deliveryData);
      return response.data;
    } catch (error) {
      console.error('Error logging delivery:', error);
      throw error;
    }
  },

  getDeliveryLogs: async (params = {}) => {
    try {
      const response = await apiClient.get('/deliveries', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching delivery logs:', error);
      throw error;
    }
  },

  updateDeliveryStatus: async (deliveryId, statusData) => {
    try {
      const response = await apiClient.put(`/deliveries/${deliveryId}/status`, statusData);
      return response.data;
    } catch (error) {
      console.error('Error updating delivery status:', error);
      throw error;
    }
  },

  uploadDeliveryPhoto: async (deliveryId, photoData) => {
    try {
      const response = await apiClient.post(`/deliveries/${deliveryId}/photos`, photoData);
      return response.data;
    } catch (error) {
      console.error('Error uploading delivery photo:', error);
      throw error;
    }
  },

  captureSignature: async (deliveryId, signatureData) => {
    try {
      const response = await apiClient.post(`/deliveries/${deliveryId}/signature`, signatureData);
      return response.data;
    } catch (error) {
      console.error('Error capturing signature:', error);
      throw error;
    }
  },

  // Route Optimization
  getOptimizedRoutes: async (routeParams) => {
    try {
      const response = await apiClient.post('/routes/optimize', routeParams);
      return response.data;
    } catch (error) {
      console.error('Error getting optimized routes:', error);
      throw error;
    }
  },

  getNearbyClients: async (location, params = {}) => {
    try {
      const response = await apiClient.get('/clients/nearby', {
        params: { ...params, lat: location.lat, lng: location.lng }
      });
      return response.data;
    } catch (error) {
      console.error('Error getting nearby clients:', error);
      throw error;
    }
  },

  // Pickup Coordination
  createPickupBatch: async (pickupData) => {
    try {
      const response = await apiClient.post('/pickups/batch', pickupData);
      return response.data;
    } catch (error) {
      console.error('Error creating pickup batch:', error);
      throw error;
    }
  },

  getPickupRequests: async (params = {}) => {
    try {
      const response = await apiClient.get('/pickups', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching pickup requests:', error);
      throw error;
    }
  },

  assignVehicleToPickup: async (pickupId, vehicleData) => {
    try {
      const response = await apiClient.post(`/pickups/${pickupId}/assign`, vehicleData);
      return response.data;
    } catch (error) {
      console.error('Error assigning vehicle to pickup:', error);
      throw error;
    }
  },

  updatePickupStatus: async (pickupId, statusData) => {
    try {
      const response = await apiClient.put(`/pickups/${pickupId}/status`, statusData);
      return response.data;
    } catch (error) {
      console.error('Error updating pickup status:', error);
      throw error;
    }
  },

  // Client Analytics
  getClientAnalytics: async (clientId, params = {}) => {
    try {
      const response = await apiClient.get(`/clients/${clientId}/analytics`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching client analytics:', error);
      throw error;
    }
  },

  getClientPerformanceMetrics: async (params = {}) => {
    try {
      const response = await apiClient.get('/clients/performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching client performance metrics:', error);
      throw error;
    }
  },

  // Notification Management
  sendNotification: async (notificationData) => {
    try {
      const response = await apiClient.post('/notifications', notificationData);
      return response.data;
    } catch (error) {
      console.error('Error sending notification:', error);
      throw error;
    }
  },

  // Operational Analytics
  getFleetUtilization: async (params = {}) => {
    try {
      const response = await apiClient.get('/analytics/fleet-utilization', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching fleet utilization:', error);
      throw error;
    }
  },

  getDriverProductivity: async (params = {}) => {
    try {
      const response = await apiClient.get('/analytics/driver-productivity', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching driver productivity:', error);
      throw error;
    }
  },

  getPeakDemandAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/analytics/peak-demand', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching peak demand analysis:', error);
      throw error;
    }
  },

  getGeographicHeatMaps: async (params = {}) => {
    try {
      const response = await apiClient.get('/analytics/geographic-heatmaps', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching geographic heat maps:', error);
      throw error;
    }
  },

  getExceptionHandlingReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/analytics/exception-handling', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching exception handling reports:', error);
      throw error;
    }
  },

  getResourceAllocationOptimization: async (params = {}) => {
    try {
      const response = await apiClient.get('/analytics/resource-allocation', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching resource allocation optimization:', error);
      throw error;
    }
  },

  // Accounting Integration Reports
  getGeneralLedgerReconciliation: async (params = {}) => {
    try {
      const response = await apiClient.get('/accounting/general-ledger-reconciliation', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching general ledger reconciliation:', error);
      throw error;
    }
  },

  getAccountsReceivableAging: async (params = {}) => {
    try {
      const response = await apiClient.get('/accounting/accounts-receivable-aging', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching accounts receivable aging:', error);
      throw error;
    }
  },

  getAccountsPayableAging: async (params = {}) => {
    try {
      const response = await apiClient.get('/accounting/accounts-payable-aging', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching accounts payable aging:', error);
      throw error;
    }
  },

  getTaxReportingCompliance: async (params = {}) => {
    try {
      const response = await apiClient.get('/accounting/tax-reporting-compliance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching tax reporting compliance:', error);
      throw error;
    }
  },

  getAuditTrails: async (params = {}) => {
    try {
      const response = await apiClient.get('/accounting/audit-trails', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching audit trails:', error);
      throw error;
    }
  },

  getCostCenterAnalysis: async (params = {}) => {
    try {
      const response = await apiClient.get('/accounting/cost-center-analysis', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cost center analysis:', error);
      throw error;
    }
  },

  getFinancialStatements: async (params = {}) => {
    try {
      const response = await apiClient.get('/accounting/financial-statements', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching financial statements:', error);
      throw error;
    }
  }
};

export default clientService;